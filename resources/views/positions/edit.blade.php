<x-app-layout>
    <x-slot name="header">
        <div class="flex w-full flex-col gap-3">
            <h1 class="text-3xl font-semibold text-slate-900 dark:text-white">Edit Posisi</h1>
            <nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                <a href="{{ route('dashboard') }}" class="hover:text-indigo-600 focus:text-indigo-600 dark:hover:text-indigo-300 dark:focus:text-indigo-300">Dashboard</a>
                <span>/</span>
                <a href="{{ route('positions.index') }}" class="hover:text-indigo-600 focus:text-indigo-600 dark:hover:text-indigo-300 dark:focus:text-indigo-300">Posisi</a>
                <span>/</span>
                <span class="text-slate-700 dark:text-slate-200">{{ $position->name }}</span>
            </nav>
        </div>
    </x-slot>

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <h2 class="text-xl font-semibold text-slate-900 dark:text-white">Perbarui Data Posisi</h2>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Ubah detail posisi sesuai kebutuhan.</p>

        @if ($errors->any())
            <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-900/40 dark:bg-rose-900/20 dark:text-rose-200">
                <ul class="list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('positions.update', $position) }}" method="POST" class="mt-6 space-y-6">
            @csrf
            @method('PUT')

            <div class="space-y-2">
                <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Slug Posisi</label>
                <input type="text" name="slug" value="{{ old('slug', $position->slug) }}" required placeholder="contoh: pengurus_pusat, guest, anggota" class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm text-slate-900 shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:focus:border-indigo-400">
                <p class="text-xs text-slate-500 dark:text-slate-400">Gunakan huruf kecil, angka, dan underscore saja. Ini digunakan untuk identifikasi internal.</p>
            </div>

            <div class="space-y-2">
                <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Nama Posisi</label>
                <input type="text" name="name" value="{{ old('name', $position->name) }}" required placeholder="contoh: Pengurus Pusat, Tamu, Anggota" class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm text-slate-900 shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:focus:border-indigo-400">
                <p class="text-xs text-slate-500 dark:text-slate-400">Nama yang akan ditampilkan kepada pengguna.</p>
            </div>

            <label class="inline-flex items-center gap-3">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $position->is_active)) class="h-5 w-5 rounded border-slate-300 text-indigo-600 focus:ring focus:ring-indigo-200 dark:border-slate-700 dark:bg-slate-800 dark:text-indigo-400">
                <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Posisi aktif</span>
            </label>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('positions.index') }}" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:text-slate-800 dark:border-slate-700 dark:text-slate-300 dark:hover:border-slate-600 dark:hover:text-slate-100">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700">
                    <i class="fa-solid fa-check text-xs"></i>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
