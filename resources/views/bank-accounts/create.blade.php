<x-app-layout>
    <x-slot name="header">
        <div class="flex w-full flex-col gap-3">
            <h1 class="text-3xl font-semibold text-slate-900 dark:text-white">Tambah Rekening Bank</h1>
            <nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                <a href="{{ route('dashboard') }}" class="hover:text-indigo-600">Dashboard</a>
                <span>/</span>
                <a href="{{ route('bank-accounts.index') }}" class="hover:text-indigo-600">Rekening Bank</a>
                <span>/</span>
                <span class="text-slate-700 dark:text-slate-200">Tambah</span>
            </nav>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <form action="{{ route('bank-accounts.store') }}" method="POST" enctype="multipart/form-data" data-bank-account-form="create">
                @csrf
                <div class="grid gap-4">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <label class="block">
                            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Nama Bank</span>
                            <input
                                type="text"
                                name="nama_bank"
                                id="nama_bank"
                                value="{{ old('nama_bank') }}"
                                class="mt-1 w-full rounded-md border-slate-200 px-3 py-2"
                                placeholder="Misal: BCA, Mandiri, BNI"
                                required
                            >
                            @error('nama_bank')
                                <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                        </label>

                        <label class="block">
                            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Pemilik Rekening</span>
                            <input
                                type="text"
                                name="nama"
                                value="{{ old('nama') }}"
                                class="mt-1 w-full rounded-md border-slate-200 px-3 py-2"
                                placeholder="Nama pemilik rekening"
                                required
                            >
                            @error('nama')
                                <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                        </label>
                    </div>

                    <label class="block sm:w-1/2">
                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Nomor Rekening</span>
                        <input
                            type="text"
                            name="no_rek"
                            value="{{ old('no_rek') }}"
                            class="mt-1 w-full rounded-md border-slate-200 px-3 py-2"
                            placeholder="1234567890"
                            required
                        >
                        @error('no_rek')
                            <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </label>

                    <label class="block">
                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Logo Bank (Opsional)</span>
                        <input
                            type="file"
                            name="photo"
                            id="photo-input"
                            accept="image/*"
                            class="mt-1"
                        >
                        <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Ukuran maksimum 2 MB, akan dikompresi otomatis ke sekitar 250 KB.</p>
                        <div id="photo-alert" class="mt-2 hidden rounded-lg border border-amber-300 bg-amber-50 px-3 py-2 text-xs text-amber-700"></div>
                        @error('photo')
                            <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </label>

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ request('redirect', route('bank-accounts.index')) }}" class="inline-flex items-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800/80">
                            Batal
                        </a>
                        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700">
                            Simpan Rekening
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('form[data-bank-account-form="create"]');
            const photoInput = document.getElementById('photo-input');
            const alertBox = document.getElementById('photo-alert');
            const sizeLimit = 2 * 1024 * 1024; // 2 MB

            function showPhotoError(message) {
                if (!alertBox) return;
                alertBox.textContent = message;
                alertBox.classList.remove('hidden');
            }

            function clearPhotoError() {
                if (!alertBox) return;
                alertBox.textContent = '';
                alertBox.classList.add('hidden');
            }

            if (photoInput) {
                photoInput.addEventListener('change', function () {
                    clearPhotoError();

                    if (this.files && this.files[0] && this.files[0].size > sizeLimit) {
                        showPhotoError('Ukuran file melebihi 2 MB. Silakan pilih gambar lain.');
                        this.value = '';
                    }
                });
            }

            if (form) {
                form.addEventListener('submit', function (event) {
                    if (photoInput && photoInput.files && photoInput.files[0] && photoInput.files[0].size > sizeLimit) {
                        event.preventDefault();
                        showPhotoError('Ukuran file melebihi 2 MB. Silakan pilih gambar lain.');
                        photoInput.value = '';
                    }
                });
            }
        });
    </script>
</x-app-layout>
