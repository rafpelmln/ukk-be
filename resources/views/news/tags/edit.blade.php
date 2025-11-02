<x-app-layout>
    <x-slot name="header">
        <div class="flex w-full flex-col gap-3">
            <h1 class="text-3xl font-semibold text-slate-900 dark:text-white">Manajemen Tag</h1>
            <nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                <a href="{{ route('dashboard') }}" class="hover:text-indigo-600 focus:text-indigo-600 dark:hover:text-indigo-300 dark:focus:text-indigo-300">Dashboard</a>
                <span>/</span>
                <a href="{{ route('news.tags.index') }}" class="hover:text-indigo-600 focus:text-indigo-600 dark:hover:text-indigo-300 dark:focus:text-indigo-300">News</a>
                <span>/</span>
                <a href="{{ route('news.tags.index') }}" class="hover:text-indigo-600 focus:text-indigo-600 dark:hover:text-indigo-300 dark:focus:text-indigo-300">Tag</a>
                <span>/</span>
                <span class="text-slate-700 dark:text-slate-200">Edit</span>
            </nav>
        </div>
    </x-slot>

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-slate-900 dark:text-white">Edit Tag</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Perbarui detail tag sesuai kebutuhan.</p>
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

        <form action="{{ route('news.tags.update', $tag) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')
            <input type="hidden" name="redirect" value="{{ request('redirect', route('news.tags.index')) }}">

            <div class="grid gap-6 md:grid-cols-2">
                <div class="space-y-2">
                    <label for="name" class="text-sm font-medium text-slate-700 dark:text-slate-200">Nama Tag <span class="text-rose-500">*</span></label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $tag->name) }}"
                        required
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 shadow-sm transition focus:border-indigo-400 focus:bg-white focus:outline-none focus:ring focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:border-indigo-500"
                        placeholder="Masukkan nama tag"
                    >
                </div>

            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="text-sm font-medium text-slate-700 dark:text-slate-200">Status aktif</span>
                    <label class="relative inline-flex cursor-pointer items-center">
                        <input type="hidden" name="is_active" value="0">
                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            class="peer sr-only"
                            {{ old('is_active', $tag->is_active ? '1' : '0') === '1' ? 'checked' : '' }}
                        >
                        <span class="block h-6 w-10 rounded-full bg-slate-300 transition peer-checked:bg-emerald-500 dark:bg-slate-700 dark:peer-checked:bg-emerald-400"></span>
                        <span class="pointer-events-none absolute left-1 top-1 block h-4 w-4 rounded-full bg-white transition peer-checked:translate-x-4"></span>
                    </label>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ request('redirect', route('news.tags.index')) }}" class="inline-flex items-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800/80">Batal</a>
                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700">
                        <i class="fa-solid fa-check"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
