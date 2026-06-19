<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laragear\WebAuthn\Assertion\Validator\AssertionValidation;
use Laragear\WebAuthn\Assertion\Validator\AssertionValidator;
use Laragear\WebAuthn\Http\Requests\AssertionRequest;
use Laragear\WebAuthn\Http\Requests\AttestationRequest;
use Laragear\WebAuthn\Http\Requests\AttestedRequest;
use Laragear\WebAuthn\JsonTransport;

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

    /** Verifikasi login passkey (assertion) — tampilkan penyebab gagal. */
    public function loginVerify(Request $request, AssertionValidator $validator): JsonResponse
    {
        try {
            $validation = $validator
                ->send(new AssertionValidation(new JsonTransport($request->all())))
                ->thenReturn();

            $credential = $validation->credential;

            if (! $credential) {
                return response()->json(['ok' => false, 'error' => 'Credential tidak ditemukan.'], 422);
            }

            $credential->last_login_at = now();
            $credential->save();

            Auth::login($credential->authenticatable, true);

            return response()->json(['ok' => true]);
        } catch (\Throwable $e) {
            Log::error('Passkey login gagal: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json(['ok' => false, 'error' => $e->getMessage()], 422);
        }
    }
}
