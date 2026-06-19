<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laragear\WebAuthn\Contracts\WebAuthnAuthenticatable;
use Laragear\WebAuthn\WebAuthnAuthentication;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'role'])]
class Admin extends Authenticatable implements WebAuthnAuthenticatable
{
    use HasRoles, WebAuthnAuthentication;

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function isSuperadmin(): bool
    {
        return $this->hasRole('superadmin');
    }

    /** Relasi credential passkey (alias agar mudah dipanggil). */
    public function passkeys()
    {
        return $this->webauthnCredentials();
    }
}
