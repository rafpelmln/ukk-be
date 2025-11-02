<x-app-layout>
    <x-slot name="header">
        <div class="flex w-full flex-col gap-3">
            <h1 class="text-3xl font-semibold text-slate-900 dark:text-white">Tambah Kategori Berita</h1>
            <nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                <a href="{{ route('dashboard') }}" class="hover:text-indigo-600 focus:text-indigo-600 dark:hover:text-indigo-300 dark:focus:text-indigo-300">Dashboard</a>
                <span>/</span>
                <a href="{{ route('news.categories.index') }}" class="hover:text-indigo-600 focus:text-indigo-600 dark:hover:text-indigo-300 dark:focus:text-indigo-300">Kategori Berita</a>
                <span>/</span>
                <span class="text-slate-700 dark:text-slate-200">Tambah</span>
            </nav>
        </div>
    </x-slot>

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-slate-900 dark:text-white">Form Kategori Baru</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Lengkapi data kategori untuk memudahkan pengelompokan berita.</p>
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-900/50 dark:bg-rose-900/20 dark:text-rose-200">
                <p class="font-semibold">Periksa kembali data yang diisi:</p>
                <ul class="mt-2 list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('news.categories.store') }}" method="POST" class="space-y-8">
            @csrf

            <div class="grid gap-6 md:grid-cols-2">
                <div class="space-y-2">
                    <label for="name" class="text-sm font-medium text-slate-700 dark:text-slate-200">Nama Kategori <span class="text-rose-500">*</span></label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 shadow-sm transition focus:border-indigo-400 focus:bg-white focus:outline-none focus:ring focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:border-indigo-500"
                        placeholder="Contoh: Acara Organisasi"
                    >
                </div>

                <div class="space-y-2">
                    <label for="color" class="text-sm font-medium text-slate-700 dark:text-slate-200">Warna Kategori <span class="text-rose-500">*</span></label>
                    <div class="flex items-center gap-3">
                        <input
                            type="color"
                            id="color"
                            name="color"
                            value="{{ old('color', '#603ba5') }}"
                            required
                            class="h-12 w-20 cursor-pointer rounded-lg border border-slate-200 bg-white p-1 dark:border-slate-700 dark:bg-slate-800"
                            oninput="document.querySelector('#color-text').value = this.value"
                            onchange="document.querySelector('#color-text').value = this.value"
                        >
                        <input
                            type="text"
                            id="color-text"
                            inputmode="text"
                            pattern="^#[0-9A-Fa-f]{6}$"
                            maxlength="7"
                            value="{{ old('color', '#603ba5') }}"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 shadow-sm transition focus:border-indigo-400 focus:bg-white focus:outline-none focus:ring focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:border-indigo-500"
                            placeholder="#603BA5"
                            oninput="document.querySelector('#color').value = this.value"
                            onchange="document.querySelector('#color').value = this.value"
                        >
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Gunakan kode warna heksadesimal, misal <code>#603BA5</code>.</p>
                </div>
            </div>

            <div class="space-y-2">
                <label for="description" class="text-sm font-medium text-slate-700 dark:text-slate-200">Deskripsi</label>
                <textarea
                    id="description"
                    name="description"
                    rows="4"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 shadow-sm transition focus:border-indigo-400 focus:bg-white focus:outline-none focus:ring focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:border-indigo-500"
                    placeholder="Tuliskan deskripsi singkat kategori..."
                >{{ old('description') }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a
                    href="{{ route('news.categories.index') }}"
                    class="inline-flex items-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800/80"
                >
                    Batal
                </a>
                <button
                    type="submit"
                    class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow transition hover:bg-indigo-700"
                >
                    <i class="fa-solid fa-check"></i>
                    Simpan Kategori
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
