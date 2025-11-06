<x-app-layout>
    <x-slot name="header">
        <div class="flex w-full flex-col gap-3">
            <h1 class="text-3xl font-semibold text-slate-900 dark:text-white">Banner Beranda</h1>
            <nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                <a href="{{ route('dashboard') }}" class="hover:text-indigo-600">Dashboard</a>
                <span>/</span>
                <span class="text-slate-700 dark:text-slate-200">Banner Beranda</span>
            </nav>
        </div>
    </x-slot>

    @php
        $currentSort = $sort ?? 'display_order';
        $currentDirection = $direction ?? 'asc';
        $perPageOption = $perPage ?? 10;
        $queryParams = request()->except(['page', 'sort', 'direction']);
        $persistParams = request()->except(['page']);
        $currentListingUrl = request()->fullUrl();

        $sortUrl = function (string $column) use ($queryParams, $currentSort, $currentDirection) {
            $isActive = $currentSort === $column;
            $nextDirection = $isActive && $currentDirection === 'asc' ? 'desc' : 'asc';

            return route('home-banners.index', array_merge($queryParams, [
                'sort' => $column,
                'direction' => $nextDirection,
            ]));
        };

        $sortIcon = function (string $column) use ($currentSort, $currentDirection) {
            if ($currentSort !== $column) {
                return 'fa-sort';
            }

            return $currentDirection === 'asc' ? 'fa-sort-up' : 'fa-sort-down';
        };
    @endphp

    <div class="space-y-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <form method="GET" action="{{ route('home-banners.index') }}" class="relative w-full max-w-xs">
                    <label for="banner-search" class="sr-only">Cari banner</label>
                    <input type="hidden" name="sort" value="{{ $currentSort }}">
                    <input type="hidden" name="direction" value="{{ $currentDirection }}">
                    <input type="hidden" name="per_page" value="{{ $perPageOption }}">
                    <input
                        type="search"
                        id="banner-search"
                        name="query"
                        value="{{ $search ?? '' }}"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pl-11 pr-4 text-sm text-slate-700 shadow-sm transition focus:border-indigo-400 focus:bg-white focus:outline-none focus:ring focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:border-indigo-500"
                        placeholder="Cari banner..."
                    >
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                </form>

                <div class="flex flex-wrap items-center gap-3">
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-4 py-2 text-sm text-slate-600 transition hover:border-indigo-300 hover:text-indigo-600 dark:border-slate-700 dark:text-slate-300 dark:hover:border-indigo-400 dark:hover:text-indigo-300"
                        x-data
                        x-on:click="$dispatch('open-banner-info')"
                    >
                        <i class="fa-solid fa-circle-info"></i>
                        Petunjuk Upload
                    </button>
                    <a href="{{ route('home-banners.create', array_merge($persistParams, ['redirect' => $currentListingUrl])) }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700">
                        <x-icon name="plus" class="h-4 w-4" />
                        Tambah Banner
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-400/40 dark:bg-emerald-900/40 dark:text-emerald-200">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mt-6 space-y-4">
                <div
                    x-data="{ open: false }"
                    x-on:open-banner-info.window="open = true"
                    x-show="open"
                    x-cloak
                    class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600 dark:border-slate-700 dark:bg-slate-800/60 dark:text-slate-300"
                >
                    <div class="flex items-start gap-3">
                        <i class="fa-solid fa-lightbulb mt-1 text-indigo-500"></i>
                        <div class="space-y-1">
                            <p class="font-semibold text-slate-700 dark:text-slate-100">Tips upload banner:</p>
                            <ul class="list-inside list-disc space-y-1">
                                <li>Gunakan rasio 16:9 agar sesuai dengan slider home.</li>
                                <li>Ukuran file maksimal <span class="font-semibold">2MB</span>.</li>
                                <li>Gambar otomatis dikompres menjadi JPG saat disimpan.</li>
                                <li>Isi tombol opsional, kosongkan jika tidak diperlukan.</li>
                            </ul>
                            <button type="button" class="mt-2 text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-300" x-on:click="open = false">Tutup</button>
                        </div>
                    </div>
                </div>

                @if ($banners->count())
                    <div class="overflow-hidden rounded-xl border border-slate-200 dark:border-slate-700">
                        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                            <thead class="bg-slate-50 dark:bg-slate-800/60">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                        Banner
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                        <a href="{{ $sortUrl('title') }}" class="flex w-full items-center gap-2 rounded-md px-1 py-1 hover:bg-indigo-50 hover:text-indigo-600 dark:hover:bg-slate-800/70 dark:hover:text-indigo-300">
                                            Judul
                                            <i class="fa-solid {{ $sortIcon('title') }} text-slate-400"></i>
                                        </a>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                        <a href="{{ $sortUrl('display_order') }}" class="flex w-full items-center gap-2 rounded-md px-1 py-1 hover:bg-indigo-50 hover:text-indigo-600 dark:hover:bg-slate-800/70 dark:hover:text-indigo-300">
                                            Urutan
                                            <i class="fa-solid {{ $sortIcon('display_order') }} text-slate-400"></i>
                                        </a>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                        <a href="{{ $sortUrl('is_active') }}" class="flex w-full items-center gap-2 rounded-md px-1 py-1 hover:bg-indigo-50 hover:text-indigo-600 dark:hover:bg-slate-800/70 dark:hover:text-indigo-300">
                                            Status
                                            <i class="fa-solid {{ $sortIcon('is_active') }} text-slate-400"></i>
                                        </a>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white dark:divide-slate-700 dark:bg-slate-900">
                                @foreach ($banners as $banner)
                                    <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/60">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="h-16 w-28 overflow-hidden rounded-lg border border-slate-200 bg-slate-100 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                                                    @if ($banner->image_path)
                                                        <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" class="h-full w-full object-cover">
                                                    @else
                                                        <div class="flex h-full items-center justify-center text-xs text-slate-400">Tidak ada gambar</div>
                                                    @endif
                                                </div>
                                                <div class="text-sm text-slate-600 dark:text-slate-300">
                                                    <p class="font-semibold text-slate-700 dark:text-slate-100">{{ $banner->title }}</p>
                                                    @if ($banner->subtitle)
                                                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ $banner->subtitle }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">
                                            {{ $banner->description ? \Illuminate\Support\Str::limit($banner->description, 80) : '—' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">
                                            {{ $banner->display_order }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-medium {{ $banner->is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300' : 'bg-slate-200 text-slate-600 dark:bg-slate-700 dark:text-slate-300' }}">
                                                <span class="h-2 w-2 rounded-full {{ $banner->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                                {{ $banner->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center justify-center gap-3 text-base">
                                                <form action="{{ route('home-banners.toggle', $banner) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="text-slate-500 transition hover:text-indigo-600" title="Ubah status">
                                                        <i class="fa-solid fa-toggle-{{ $banner->is_active ? 'on text-emerald-500' : 'off' }}"></i>
                                                    </button>
                                                </form>
                                                <a href="{{ route('home-banners.edit', $banner) }}" class="text-slate-500 transition hover:text-indigo-600" title="Edit">
                                                    <i class="fa-solid fa-pen"></i>
                                                </a>
                                                <form action="{{ route('home-banners.destroy', $banner) }}" method="POST" onsubmit="return confirm('Hapus banner ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-slate-500 transition hover:text-rose-600" title="Hapus">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex flex-wrap items-center gap-3 text-sm text-slate-500 dark:text-slate-400">
                            <form method="GET" action="{{ route('home-banners.index') }}" class="flex items-center gap-2" id="banners-per-page-form">
                                <span>Tampilkan</span>
                                <select
                                    name="per_page"
                                    class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 transition hover:border-indigo-400 focus:border-indigo-400 focus:outline-none focus:ring focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:border-indigo-400 dark:focus:border-indigo-400"
                                    onchange="this.form.submit()"
                                >
                                    @foreach ([5, 10, 25, 50, 100] as $option)
                                        <option value="{{ $option }}" @selected($perPageOption === $option)>{{ $option }}</option>
                                    @endforeach
                                </select>
                                <span>per halaman</span>
                                <input type="hidden" name="query" value="{{ $search ?? '' }}">
                                <input type="hidden" name="sort" value="{{ $currentSort }}">
                                <input type="hidden" name="direction" value="{{ $currentDirection }}">
                            </form>
                            <span>
                                Menampilkan
                                <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $banners->firstItem() }}</span>
                                sampai
                                <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $banners->lastItem() }}</span>
                                dari
                                <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $banners->total() }}</span>
                                banner
                            </span>
                        </div>
                        <div>
                            {{ $banners->onEachSide(1)->links('vendor.pagination.tailwind-simple') }}
                        </div>
                    </div>
                @else
                    <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-10 text-center text-sm text-slate-500 dark:border-slate-700 dark:bg-slate-900/50 dark:text-slate-300">
                        @if (!empty($search))
                            Tidak ditemukan banner dengan kata kunci <span class="font-semibold text-slate-700 dark:text-slate-200">"{{ $search }}"</span>.
                        @else
                            Belum ada banner yang ditambahkan. Klik tombol "Tambah Banner" untuk membuat yang baru.
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
