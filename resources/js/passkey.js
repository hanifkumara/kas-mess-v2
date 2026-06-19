// Passkey (WebAuthn) client helper — native, no deps.
// Server (laragear/webauthn) returns the PublicKey options object at top-level JSON.

function csrf() {
    return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
}

function b64uToBuf(b64u) {
    const b64 = b64u.replace(/-/g, '+').replace(/_/g, '/');
    const pad = b64.length % 4;
    return Uint8Array.from(atob(pad ? b64 + '='.repeat(4 - pad) : b64), (c) => c.charCodeAt(0));
}

function bufToB64(buf) {
    let s = '';
    const bytes = new Uint8Array(buf);
    for (let i = 0; i < bytes.byteLength; i++) s += String.fromCharCode(bytes[i]);
    return btoa(s);
}

async function post(url, data) {
    const res = await fetch(url, {
        method: 'POST',
        credentials: 'same-origin',
        redirect: 'error',
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrf(),
        },
        body: JSON.stringify(data ?? {}),
    });
    if (!res.ok) {
        const body = await res.json().catch(() => ({}));
        throw new Error(body?.message || `Permintaan gagal (HTTP ${res.status})`);
    }
    return res.json().catch(() => ({}));
}

function prepareCreation(pk) {
    pk.challenge = b64uToBuf(pk.challenge);
    if (pk.user) pk.user.id = b64uToBuf(pk.user.id);
    if (pk.excludeCredentials) {
        pk.excludeCredentials = pk.excludeCredentials.map((c) => ({ ...c, id: b64uToBuf(c.id) }));
    }
    return pk;
}

function prepareRequest(pk) {
    pk.challenge = b64uToBuf(pk.challenge);
    if (pk.allowCredentials) {
        pk.allowCredentials = pk.allowCredentials.map((c) => ({ ...c, id: b64uToBuf(c.id) }));
    }
    return pk;
}

/**
 * Daftarkan passkey baru untuk user yang sedang login.
 * @param {object} routes - {options, store}
 * @param {string} alias - nama passkey (opsional)
 */
export async function register(routes, alias = '') {
    const pk = prepareCreation(await post(routes.options, { alias }));
    const cred = await navigator.credentials.create({ publicKey: pk });

    const payload = {
        id: cred.id,
        type: cred.type,
        rawId: bufToB64(cred.rawId),
        response: {
            attestationObject: bufToB64(cred.response.attestationObject),
            clientDataJSON: bufToB64(cred.response.clientDataJSON),
        },
    };
    if (cred.response.getTransports) payload.response.transports = cred.response.getTransports();

    return post(routes.store, { ...payload, alias });
}

/**
 * Login dengan passkey (userless / discoverable).
 * @param {object} routes - {options, verify}
 */
export async function login(routes) {
    const pk = prepareRequest(await post(routes.options, {}));
    const cred = await navigator.credentials.get({ publicKey: pk });

    const payload = {
        id: cred.id,
        type: cred.type,
        rawId: bufToB64(cred.rawId),
        response: {
            authenticatorData: bufToB64(cred.response.authenticatorData),
            clientDataJSON: bufToB64(cred.response.clientDataJSON),
            signature: bufToB64(cred.response.signature),
        },
    };
    if (cred.response.userHandle) payload.response.userHandle = bufToB64(cred.response.userHandle);

    return post(routes.verify, payload);
}

export function supported() {
    return typeof window.PublicKeyCredential !== 'undefined';
}
