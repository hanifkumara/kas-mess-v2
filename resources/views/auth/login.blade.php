<!DOCTYPE html>
<html lang="id" class="h-full bg-navy-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk · Kas Mess</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>💰</text></svg>">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full">
    <div class="flex min-h-full items-center justify-center bg-gradient-to-br from-navy-900 via-navy-800 to-navy-950 px-4 py-12">
        <div class="w-full max-w-md">
            <div class="mb-8 text-center">
                <span class="inline-grid h-14 w-14 place-items-center rounded-2xl bg-white/10 text-3xl">💰</span>
                <h1 class="mt-4 text-2xl font-bold text-white">Kas Mess</h1>
                <p class="mt-1 text-sm text-navy-300">Masuk untuk mengelola kas bulanan</p>
            </div>

            <div class="rounded-2xl bg-white p-8 shadow-2xl">
                @if ($errors->any())
                    <div class="mb-5 rounded-xl bg-rose-50 px-4 py-3 text-sm text-rose-700 ring-1 ring-rose-200">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login.attempt') }}">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" autofocus required
                                class="mt-1 block w-full rounded-xl border-slate-200 text-sm shadow-sm focus:border-navy-500 focus:ring-navy-500"
                                placeholder="admin@mess.com">
                        </div>
                        <div>
                            <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                            <input id="password" type="password" name="password" required
                                class="mt-1 block w-full rounded-xl border-slate-200 text-sm shadow-sm focus:border-navy-500 focus:ring-navy-500"
                                placeholder="••••••••">
                        </div>
                        <label class="flex items-center gap-2 text-sm text-slate-600">
                            <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-slate-300 text-navy-700 focus:ring-navy-500">
                            Ingat saya
                        </label>
                    </div>

                    <button type="submit"
                        class="mt-6 w-full rounded-xl bg-navy-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-navy-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-navy-700">
                        Masuk
                    </button>
                </form>

                <div class="relative my-6 text-center">
                    <span class="relative z-10 bg-white px-3 text-xs text-slate-400">atau</span>
                    <div class="absolute inset-x-0 top-1/2 h-px bg-slate-200"></div>
                </div>

                <div x-data="passkeyLogin()" class="-mt-2">
                    <p x-show="error" x-text="error" class="mb-3 rounded-lg bg-rose-50 px-3 py-2 text-xs text-rose-700"></p>
                    <button type="button" @click="login()" :disabled="busy"
                        class="flex w-full items-center justify-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-navy-800 ring-1 ring-inset ring-slate-300 transition hover:bg-slate-50 disabled:opacity-50">
                        <span x-show="!busy">🔑 Masuk dengan Passkey</span>
                        <span x-show="busy">Memverifikasi…</span>
                    </button>
                </div>
            </div>

            <p class="mt-6 text-center text-xs text-navy-400">Laravel {{ app()->version() }} · Kas Mess</p>
        </div>
    </div>

    <script>
        function passkeyLogin() {
            return {
                busy: false,
                error: '',
                routes: {
                    options: "{{ route('passkey.login.options') }}",
                    verify: "{{ route('passkey.login') }}",
                },
                async login() {
                    this.error = '';
                    if (!window.passkey || !window.passkey.supported()) {
                        this.error = 'Browser ini tidak mendukung passkey.';
                        return;
                    }
                    this.busy = true;
                    try {
                        const res = await window.passkey.login(this.routes);
                        if (res && res.ok) {
                            window.location.href = "{{ route('dashboard') }}";
                        } else {
                            this.error = 'Login passkey gagal.';
                        }
                    } catch (e) {
                        this.error = e.message || 'Login passkey dibatalkan atau gagal.';
                    } finally {
                        this.busy = false;
                    }
                },
            };
        }
    </script>
</body>
</html>
