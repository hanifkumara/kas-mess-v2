<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Laragear\WebAuthn\Http\Requests\AssertedRequest;
use Laragear\WebAuthn\Http\Requests\AssertionRequest;
use Laragear\WebAuthn\Http\Requests\AttestationRequest;
use Laragear\WebAuthn\Http\Requests\AttestedRequest;

class WebAuthnController extends Controller
{
    /** Opsi pembuatan passkey (attestation) — butuh login. */
    public function registerOptions(AttestationRequest $request)
    {
        return $request->toCreate();
    }

    /** Simpan passkey baru (attestation). */
    public function registerStore(AttestedRequest $request): JsonResponse
    {
        $request->validate(['alias' => 'nullable|string|max:60']);

        $id = $request->save(['alias' => $request->input('alias', 'Passkey')]);

        return response()->json(['ok' => true, 'id' => $id]);
    }

    /** Opsi login passkey (assertion) — userless/discoverable. */
    public function loginOptions(AssertionRequest $request)
    {
        return $request->toVerify();
    }

    /** Verifikasi login passkey (assertion). */
    public function loginVerify(AssertedRequest $request): JsonResponse
    {
        $user = $request->login(remember: true);

        return response()->json(['ok' => (bool) $user]);
    }
}
