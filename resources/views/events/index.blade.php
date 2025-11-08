<x-app-layout>
    <x-slot name="header">
        <div class="flex w-full flex-col gap-3">
            <h1 class="text-3xl font-semibold text-slate-900 dark:text-white">Events</h1>
            <nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                <a href="{{ route('dashboard') }}" class="hover:text-indigo-600">Dashboard</a>
                <span>/</span>
                <span class="text-slate-700 dark:text-slate-200">Events</span>
            </nav>
        </div>
    </x-slot>

    @php
        $currentSort = $sort ?? 'created_at';
        $currentDirection = $direction ?? 'desc';
        $perPage = $perPage ?? 10;
        $queryParams = request()->except(['page', 'sort', 'direction']);
        $persistParams = request()->except(['page']);
        $currentListingUrl = request()->fullUrl();

        $sortUrl = function (string $column) use ($queryParams, $currentSort, $currentDirection) {
            $isActive = $currentSort === $column;
            $nextDirection = $isActive && $currentDirection === 'asc' ? 'desc' : 'asc';

            return route('events.index', array_merge($queryParams, [
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
                <form method="GET" action="{{ route('events.index') }}" class="relative w-full max-w-xs">
                    <label for="events-search" class="sr-only">Cari event</label>
                    <input type="hidden" name="sort" value="{{ $currentSort }}">
                    <input type="hidden" name="direction" value="{{ $currentDirection }}">
                    <input type="hidden" name="per_page" value="{{ $perPage }}">
                    <input
                        type="search"
                        id="events-search"
                        name="query"
                        value="{{ $search ?? '' }}"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pl-11 pr-4 text-sm text-slate-700 shadow-sm transition focus:border-indigo-400 focus:bg-white focus:outline-none focus:ring focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:border-indigo-500"
                        placeholder="Cari event..."
                    >
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                </form>

                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('events.create', array_merge($persistParams, ['redirect' => $currentListingUrl])) }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700">
                        <x-icon name="plus" class="h-4 w-4" />
                        Buat Event
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
            @endif

            <div class="mt-6">
                @if ($events->count())
                    <div class="overflow-hidden rounded-xl border border-slate-200 dark:border-slate-700">
                        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                            <thead class="bg-slate-50 dark:bg-slate-800/60">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                        Foto
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                        <a href="{{ $sortUrl('title') }}" class="flex w-full items-center gap-2 rounded-md px-1 py-1 hover:bg-indigo-50 hover:text-indigo-600 dark:hover:bg-slate-800/70 dark:hover:text-indigo-300">
                                            Judul
                                            <i class="fa-solid {{ $sortIcon('title') }} text-slate-400"></i>
                                        </a>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                        <a href="{{ $sortUrl('event_date') }}" class="flex w-full items-center gap-2 rounded-md px-1 py-1 hover:bg-indigo-50 hover:text-indigo-600 dark:hover:bg-slate-800/70 dark:hover:text-indigo-300">
                                            Tanggal Event
                                            <i class="fa-solid {{ $sortIcon('event_date') }} text-slate-400"></i>
                                        </a>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                        Lokasi
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                        <a href="{{ $sortUrl('created_at') }}" class="flex w-full items-center gap-2 rounded-md px-1 py-1 hover:bg-indigo-50 hover:text-indigo-600 dark:hover:bg-slate-800/70 dark:hover:text-indigo-300">
                                            Dibuat
                                            <i class="fa-solid {{ $sortIcon('created_at') }} text-slate-400"></i>
                                        </a>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white dark:divide-slate-700 dark:bg-slate-900">
                                @foreach ($events as $event)
                                    <tr>
                                        <td class="px-6 py-4">
                                            @if ($event->photo_url)
                                                <img src="{{ $event->photo_url }}" alt="{{ $event->title }}" class="h-14 w-20 rounded-lg object-cover">
                                            @else
                                                <div class="flex h-14 w-20 items-center justify-center rounded-lg border border-dashed border-slate-300 text-xs text-slate-400 dark:border-slate-700 dark:text-slate-500">Tidak ada foto</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="font-semibold text-slate-800 dark:text-slate-100">{{ $event->title }}</div>
                                            @if ($event->subtitle)
                                                <div class="text-sm text-slate-500 dark:text-slate-400">{{ $event->subtitle }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">
                                            {{ $event->event_date ? $event->event_date->format('d M Y') : '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">{{ $event->location ?? '-' }}</td>
                                        <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">{{ $event->created_at->format('d M Y H:i') }}</td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center justify-center gap-3 text-base">
                                                <a href="{{ route('events.show', $event) }}" class="text-slate-500 transition hover:text-indigo-600" title="Detail">
                                                    <i class="fa-solid fa-eye"></i>
                                                </a>
                                                <a href="{{ route('events.edit', $event) }}" class="text-slate-500 transition hover:text-indigo-600" title="Edit">
                                                    <i class="fa-solid fa-pen"></i>
                                                </a>
                                                <form action="{{ route('events.destroy', $event) }}" method="POST" onsubmit="return confirm('Hapus event ini?')">
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
                            <form method="GET" action="{{ route('events.index') }}" class="flex items-center gap-2" id="events-per-page-form">
                                <span>Tampilkan</span>
                                <select
                                    name="per_page"
                                    class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 transition hover:border-indigo-400 focus:border-indigo-400 focus:outline-none focus:ring focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:border-indigo-400 dark:focus:border-indigo-400"
                                    onchange="this.form.submit()"
                                >
                                    @foreach ([5, 10, 25, 50, 100] as $option)
                                        <option value="{{ $option }}" @selected($perPage === $option)>{{ $option }}</option>
                                    @endforeach
                                </select>
                                <span>per halaman</span>
                                <input type="hidden" name="query" value="{{ $search ?? '' }}">
                                <input type="hidden" name="sort" value="{{ $currentSort }}">
                                <input type="hidden" name="direction" value="{{ $currentDirection }}">
                            </form>
                            <span>
                                Menampilkan
                                <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $events->firstItem() }}</span>
                                sampai
                                <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $events->lastItem() }}</span>
                                dari
                                <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $events->total() }}</span>
                                event
                            </span>
                        </div>
                        <div>
                            {{ $events->onEachSide(1)->links('vendor.pagination.tailwind-simple') }}
                        </div>
                    </div>
                @else
                    <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-10 text-center text-sm text-slate-500 dark:border-slate-700 dark:bg-slate-900/50 dark:text-slate-300">
                        @if (!empty($search))
                            Tidak ditemukan event dengan kata kunci <span class="font-semibold text-slate-700 dark:text-slate-200">"{{ $search }}"</span>.
                        @else
                            Belum ada event yang tersedia. Tambahkan event baru untuk memulai.
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
