<x-app-layout>
    <x-slot name="header">
        <div class="flex w-full flex-col gap-3">
            <h1 class="text-3xl font-semibold text-slate-900 dark:text-white">Tambah Banner Beranda</h1>
            <nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                <a href="{{ route('dashboard') }}" class="hover:text-indigo-600">Dashboard</a>
                <span>/</span>
                <a href="{{ route('home-banners.index') }}" class="hover:text-indigo-600">Banner Beranda</a>
                <span>/</span>
                <span class="text-slate-700 dark:text-slate-200">Tambah</span>
            </nav>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <form action="{{ route('home-banners.store') }}" method="POST" enctype="multipart/form-data" data-banner-form="create">
                @include('home-banners._form', [
                    'banner' => null,
                    'submitLabel' => 'Simpan Banner',
                ])
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('form[data-banner-form="create"]');
            const fileInput = document.getElementById('banner-image-input');
            const alertBox = document.getElementById('banner-image-alert');
            const limit = 2 * 1024 * 1024;

            function showAlert(message) {
                if (!alertBox) return;
                alertBox.textContent = message;
                alertBox.classList.remove('hidden');
            }

            function hideAlert() {
                if (!alertBox) return;
                alertBox.textContent = '';
                alertBox.classList.add('hidden');
            }

            if (fileInput) {
                fileInput.addEventListener('change', function () {
                    hideAlert();
                    if (this.files && this.files[0] && this.files[0].size > limit) {
                        showAlert('Ukuran file melebihi 2 MB. Pilih gambar lain.');
                        this.value = '';
                    }
                });
            }

            if (form) {
                form.addEventListener('submit', function (event) {
                    hideAlert();
                    if (fileInput && fileInput.files && fileInput.files[0] && fileInput.files[0].size > limit) {
                        event.preventDefault();
                        showAlert('Ukuran file melebihi 2 MB. Pilih gambar lain.');
                        fileInput.value = '';
                    }
                });
            }
        });
    </script>
</x-app-layout>
