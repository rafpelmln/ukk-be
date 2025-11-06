<x-app-layout>
    <x-slot name="header">
        <div class="flex w-full flex-col gap-3">
            <h1 class="text-3xl font-semibold text-slate-900 dark:text-white">Rekening Bank</h1>
            <nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                <a href="{{ route('dashboard') }}" class="hover:text-indigo-600">Dashboard</a>
                <span>/</span>
                <span class="text-slate-700 dark:text-slate-200">Rekening Bank</span>
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

            return route('bank-accounts.index', array_merge($queryParams, [
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
                <form method="GET" action="{{ route('bank-accounts.index') }}" class="relative w-full max-w-xs">
                    <label for="bank-accounts-search" class="sr-only">Cari rekening bank</label>
                    <input type="hidden" name="sort" value="{{ $currentSort }}">
                    <input type="hidden" name="direction" value="{{ $currentDirection }}">
                    <input type="hidden" name="per_page" value="{{ $perPage }}">
                    <input
                        type="search"
                        id="bank-accounts-search"
                        name="query"
                        value="{{ $search ?? '' }}"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pl-11 pr-4 text-sm text-slate-700 shadow-sm transition focus:border-indigo-400 focus:bg-white focus:outline-none focus:ring focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:border-indigo-500"
                        placeholder="Cari rekening bank..."
                    >
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                </form>

                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('bank-accounts.create', array_merge($persistParams, ['redirect' => $currentListingUrl])) }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700">
                        <x-icon name="plus" class="h-4 w-4" />
                        Tambah Rekening
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
            @endif

            <div class="mt-6">
                @if ($bankAccounts->count())
                    <div class="overflow-hidden rounded-xl border border-slate-200 dark:border-slate-700">
                        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                            <thead class="bg-slate-50 dark:bg-slate-800/60">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                        Logo Bank
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                        <a href="{{ $sortUrl('nama_bank') }}" class="flex w-full items-center gap-2 rounded-md px-1 py-1 hover:bg-indigo-50 hover:text-indigo-600 dark:hover:bg-slate-800/70 dark:hover:text-indigo-300">
                                            Nama Bank
                                            <i class="fa-solid {{ $sortIcon('nama_bank') }} text-slate-400"></i>
                                        </a>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                        <a href="{{ $sortUrl('nama') }}" class="flex w-full items-center gap-2 rounded-md px-1 py-1 hover:bg-indigo-50 hover:text-indigo-600 dark:hover:bg-slate-800/70 dark:hover:text-indigo-300">
                                            Pemilik Rekening
                                            <i class="fa-solid {{ $sortIcon('nama') }} text-slate-400"></i>
                                        </a>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                        <a href="{{ $sortUrl('no_rek') }}" class="flex w-full items-center gap-2 rounded-md px-1 py-1 hover:bg-indigo-50 hover:text-indigo-600 dark:hover:bg-slate-800/70 dark:hover:text-indigo-300">
                                            No. Rekening
                                            <i class="fa-solid {{ $sortIcon('no_rek') }} text-slate-400"></i>
                                        </a>
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
                                @foreach ($bankAccounts as $bankAccount)
                                    <tr>
                                        <td class="px-6 py-4">
                                            @if ($bankAccount->photo_url)
                                                <img src="{{ $bankAccount->photo_url }}" alt="{{ $bankAccount->nama_bank }}" class="h-12 w-20 rounded-lg object-contain">
                                            @else
                                                <div class="flex h-12 w-20 items-center justify-center rounded-lg border border-dashed border-slate-300 text-xs text-slate-400 dark:border-slate-700 dark:text-slate-500">Tidak ada logo</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="font-semibold text-slate-800 dark:text-slate-100">{{ $bankAccount->nama_bank }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">
                                            {{ $bankAccount->nama }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">{{ $bankAccount->no_rek }}</td>
                                        <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">{{ $bankAccount->created_at->format('d M Y H:i') }}</td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center justify-center gap-3 text-base">
                                                <a href="{{ route('bank-accounts.edit', $bankAccount) }}" class="text-slate-500 transition hover:text-indigo-600" title="Edit">
                                                    <i class="fa-solid fa-pen"></i>
                                                </a>
                                                <form action="{{ route('bank-accounts.destroy', $bankAccount) }}" method="POST"
                                                      data-confirm-delete
                                                      data-confirm-title="Hapus Rekening Bank"
                                                      data-confirm-message="Apakah Anda yakin ingin menghapus rekening bank ini?">
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
                            <form method="GET" action="{{ route('bank-accounts.index') }}" class="flex items-center gap-2" id="bank-accounts-per-page-form">
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
                                <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $bankAccounts->firstItem() }}</span>
                                sampai
                                <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $bankAccounts->lastItem() }}</span>
                                dari
                                <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $bankAccounts->total() }}</span>
                                rekening bank
                            </span>
                        </div>
                        <div>
                            {{ $bankAccounts->onEachSide(1)->links('vendor.pagination.tailwind-simple') }}
                        </div>
                    </div>
                @else
                    <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-10 text-center text-sm text-slate-500 dark:border-slate-700 dark:bg-slate-900/50 dark:text-slate-300">
                        @if (!empty($search))
                            Tidak ditemukan rekening bank dengan kata kunci <span class="font-semibold text-slate-700 dark:text-slate-200">"{{ $search }}"</span>.
                        @else
                            Belum ada rekening bank yang tersedia. Tambahkan rekening bank baru untuk memulai.
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
