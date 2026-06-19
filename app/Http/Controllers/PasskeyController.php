<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PasskeyController extends Controller
{
    /** Halaman "Passkey Saya" — daftar & daftarkan passkey milik user login. */
    public function index(): View
    {
        $passkeys = auth()->user()->passkeys()->orderByDesc('created_at')->get();

        return view('passkeys.index', [
            'passkeys' => $passkeys,
            'admin' => auth()->user(),
        ]);
    }

    /** Hapus sebuah passkey. */
    public function destroy(string $id): RedirectResponse
    {
        $admin = auth()->user();

        $credential = $admin->passkeys()->whereKey($id)->first();

        if ($credential) {
            $credential->delete();

            return back()->with('toast', ['type' => 'success', 'message' => 'Passkey dihapus.']);
        }

        return back()->with('toast', ['type' => 'error', 'message' => 'Passkey tidak ditemukan.']);
    }
}
