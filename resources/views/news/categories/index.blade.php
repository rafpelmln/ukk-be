<x-app-layout>
    <x-slot name="header">
        <div class="flex w-full flex-col gap-3">
            <h1 class="text-3xl font-semibold text-slate-900 dark:text-white">Manajemen Kategori Berita</h1>
            <nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                <a href="{{ route('dashboard') }}" class="hover:text-indigo-600 focus:text-indigo-600 dark:hover:text-indigo-300 dark:focus:text-indigo-300">Dashboard</a>
                <span>/</span>
                <span class="text-slate-700 dark:text-slate-200">Kategori Berita</span>
            </nav>
        </div>
    </x-slot>

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-slate-900 dark:text-white">Daftar Kategori Berita</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Kelola kategori untuk pengelompokan berita pada portal.</p>
            </div>
            <a
                href="{{ route('news.categories.create') }}"
                class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow transition hover:bg-indigo-700"
            >
                <i class="fa-solid fa-plus"></i>
                Tambah Kategori
            </a>
        </div>

        @if (session('success'))
            <div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-900/50 dark:bg-emerald-900/20 dark:text-emerald-200">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mt-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-900/50 dark:bg-rose-900/20 dark:text-rose-200">
                {{ session('error') }}
            </div>
        @endif

        <div class="mt-6">
            @if ($categories->count())
                <div class="overflow-hidden rounded-xl border border-slate-200 dark:border-slate-800">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                        <thead class="bg-slate-50 dark:bg-slate-800/60">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                    Nama &amp; Deskripsi
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                    Slug
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                    Warna
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                    Dibuat
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white dark:divide-slate-800 dark:bg-slate-900">
                            @foreach ($categories as $category)
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-slate-900 dark:text-slate-100">{{ $category->name }}</div>
                                        @if ($category->description)
                                            <div class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                                                {{ \Illuminate\Support\Str::limit($category->description, 90) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">
                                        {{ $category->slug }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3 text-sm text-slate-600 dark:text-slate-300">
                                            <span
                                                class="h-6 w-6 rounded-full border border-slate-200 dark:border-slate-700"
                                                style="background-color: {{ $category->color }}"
                                            ></span>
                                            <code class="rounded bg-slate-100 px-2 py-1 text-xs dark:bg-slate-800">{{ $category->color }}</code>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if ($category->is_active)
                                            <span class="inline-flex items-center gap-2 rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-200">
                                                <i class="fa-solid fa-circle-check"></i> Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-2 rounded-full bg-slate-200 px-3 py-1 text-xs font-semibold text-slate-600 dark:bg-slate-700/60 dark:text-slate-300">
                                                <i class="fa-solid fa-circle-xmark"></i> Nonaktif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">
                                        {{ $category->created_at?->translatedFormat('d F Y') ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-end gap-2">
                                            <a
                                                href="{{ route('news.categories.edit', $category) }}"
                                                class="inline-flex items-center gap-1 rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-600 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800/80"
                                            >
                                                <i class="fa-solid fa-pen-to-square"></i>
                                                Edit
                                            </a>
                                            <form
                                                action="{{ route('news.categories.toggle', $category) }}"
                                                method="POST"
                                                class="inline-flex"
                                            >
                                                @csrf
                                                @method('PATCH')
                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center gap-1 rounded-lg border border-indigo-200 px-3 py-1.5 text-xs font-semibold text-indigo-600 transition hover:bg-indigo-50 dark:border-indigo-500/30 dark:text-indigo-300 dark:hover:bg-indigo-500/10"
                                                >
                                                    <i class="fa-solid fa-arrows-rotate"></i>
                                                    {{ $category->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                                </button>
                                            </form>
                                            <form
                                                action="{{ route('news.categories.destroy', $category) }}"
                                                method="POST"
                                                onsubmit="return confirm('Hapus kategori ini? Data tidak dapat dikembalikan.');"
                                                class="inline-flex"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center gap-1 rounded-lg border border-rose-200 px-3 py-1.5 text-xs font-semibold text-rose-600 transition hover:bg-rose-50 dark:border-rose-500/40 dark:text-rose-300 dark:hover:bg-rose-500/10"
                                                >
                                                    <i class="fa-solid fa-trash-can"></i>
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $categories->onEachSide(1)->links() }}
                </div>
            @else
                <div class="flex flex-col items-center justify-center rounded-xl border border-dashed border-slate-300 bg-slate-50 py-12 text-center dark:border-slate-700 dark:bg-slate-900/40">
                    <div class="rounded-full bg-slate-200 p-4 text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                        <i class="fa-solid fa-folder-open text-2xl"></i>
                    </div>
                    <h3 class="mt-4 text-lg font-semibold text-slate-700 dark:text-slate-200">Belum ada kategori</h3>
                    <p class="mt-2 max-w-md text-sm text-slate-500 dark:text-slate-400">Tambahkan kategori berita pertama Anda untuk membantu pengelompokan konten di portal.</p>
                    <a
                        href="{{ route('news.categories.create') }}"
                        class="mt-4 inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow transition hover:bg-indigo-700"
                    >
                        <i class="fa-solid fa-plus"></i>
                        Tambah Kategori
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
