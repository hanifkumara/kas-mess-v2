<x-layout :title="$admin->exists ? 'Edit Pengguna' : 'Tambah Pengguna'" :topbarTitle="$admin->exists ? 'Edit Pengguna' : 'Tambah Pengguna'">
    <x-page-header :title="$admin->exists ? 'Edit Pengguna' : 'Tambah Pengguna'" :subtitle="$admin->exists ? $admin->name : 'Buat akun admin baru'" />

    <x-card title="Data Pengguna" class="max-w-2xl">
        <form method="POST" action="{{ $admin->exists ? route('admins.update', $admin) : route('admins.store') }}">
            @csrf
            @if ($admin->exists)
                @method('PUT')
            @endif

            <div class="divide-y divide-slate-100">
                <div class="px-5 py-4">
                    <label class="block text-sm font-medium text-slate-700" for="name">Nama</label>
                    <input id="name" type="text" name="name" value="{{ old('name', $admin->name) }}" required autofocus
                        class="mt-1 block w-full rounded-xl border-slate-200 text-sm shadow-sm focus:border-navy-500 focus:ring-navy-500">
                    @error('name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="px-5 py-4">
                    <label class="block text-sm font-medium text-slate-700" for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $admin->email) }}" required
                        class="mt-1 block w-full rounded-xl border-slate-200 text-sm shadow-sm focus:border-navy-500 focus:ring-navy-500">
                    @error('email') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="px-5 py-4">
                    <label class="block text-sm font-medium text-slate-700" for="password">Password</label>
                    <input id="password" type="password" name="password" {{ $admin->exists ? 'placeholder="Kosongkan jika tidak diubah"' : 'required' }}
                        class="mt-1 block w-full rounded-xl border-slate-200 text-sm shadow-sm focus:border-navy-500 focus:ring-navy-500">
                    <p class="mt-1 text-xs text-slate-400">{{ $admin->exists ? 'Isi hanya jika ingin mengganti password.' : 'Minimal 6 karakter.' }}</p>
                    @error('password') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="px-5 py-4">
                    <label class="block text-sm font-medium text-slate-700" for="role">Role</label>
                    <select id="role" name="role" class="mt-1 block w-full rounded-xl border-slate-200 text-sm shadow-sm focus:border-navy-500 focus:ring-navy-500">
                        @foreach ($roles as $role)
                            <option value="{{ $role->name }}" @selected(old('role', $admin->roles->first()?->name) === $role->name)>{{ $role->name }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-slate-400">Role menentukan permission yang dimiliki pengguna.</p>
                    @error('role') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex justify-end gap-2 px-5 py-4">
                <x-button as="button" type="button" variant="secondary" :href="route('admins.index')">Batal</x-button>
                <x-button as="button" type="submit">Simpan</x-button>
            </div>
        </form>
    </x-card>
</x-layout>
