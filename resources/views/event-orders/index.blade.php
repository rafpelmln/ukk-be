<x-app-layout>
    <x-slot name="header">
        <div class="flex w-full flex-col gap-3">
            <h1 class="text-3xl font-semibold text-slate-900 dark:text-white">Order Events</h1>
            <nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                <a href="{{ route('dashboard') }}" class="hover:text-indigo-600">Dashboard</a>
                <span>/</span>
                <span class="text-slate-700 dark:text-slate-200">Order Events</span>
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

            return route('event-orders.index', array_merge($queryParams, [
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
                <div class="flex flex-wrap items-center gap-3">
                    <form method="GET" action="{{ route('event-orders.index') }}" class="relative w-full max-w-xs">
                        <label for="orders-search" class="sr-only">Cari order</label>
                        <input type="hidden" name="sort" value="{{ $currentSort }}">
                        <input type="hidden" name="direction" value="{{ $currentDirection }}">
                        <input type="hidden" name="per_page" value="{{ $perPage }}">
                        <input type="hidden" name="status" value="{{ $status }}">
                        <input
                            type="search"
                            id="orders-search"
                            name="query"
                            value="{{ $search ?? '' }}"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pl-11 pr-4 text-sm text-slate-700 shadow-sm transition focus:border-indigo-400 focus:bg-white focus:outline-none focus:ring focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:border-indigo-500"
                            placeholder="Cari order..."
                        >
                        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </span>
                    </form>

                    <form method="GET" action="{{ route('event-orders.index') }}" class="flex items-center gap-2">
                        <input type="hidden" name="query" value="{{ $search }}">
                        <input type="hidden" name="sort" value="{{ $currentSort }}">
                        <input type="hidden" name="direction" value="{{ $currentDirection }}">
                        <input type="hidden" name="per_page" value="{{ $perPage }}">
                        <select
                            name="status"
                            class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 transition hover:border-indigo-400 focus:border-indigo-400 focus:outline-none focus:ring focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                            onchange="this.form.submit()"
                        >
                            <option value="">Semua Status</option>
                            <option value="pending" @selected($status === 'pending')>Menunggu Pembayaran</option>
                            <option value="paid" @selected($status === 'paid')>Sudah Bayar</option>
                            <option value="expired" @selected($status === 'expired')>Expired</option>
                            <option value="cancelled" @selected($status === 'cancelled')>Dibatalkan</option>
                        </select>
                    </form>
                </div>
            </div>

            @if(session('success'))
                <div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="mt-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">{{ session('error') }}</div>
            @endif

            <div class="mt-6">
                @if ($orders->count())
                    <div class="overflow-hidden rounded-xl border border-slate-200 dark:border-slate-700">
                        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                            <thead class="bg-slate-50 dark:bg-slate-800/60">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                        <a href="{{ $sortUrl('order_number') }}" class="flex w-full items-center gap-2 rounded-md px-1 py-1 hover:bg-indigo-50 hover:text-indigo-600 dark:hover:bg-slate-800/70 dark:hover:text-indigo-300">
                                            Order Number
                                            <i class="fa-solid {{ $sortIcon('order_number') }} text-slate-400"></i>
                                        </a>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                        Peserta & Event
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                        <a href="{{ $sortUrl('total_amount') }}" class="flex w-full items-center gap-2 rounded-md px-1 py-1 hover:bg-indigo-50 hover:text-indigo-600 dark:hover:bg-slate-800/70 dark:hover:text-indigo-300">
                                            Total
                                            <i class="fa-solid {{ $sortIcon('total_amount') }} text-slate-400"></i>
                                        </a>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                        Metode Pembayaran
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                        <a href="{{ $sortUrl('status') }}" class="flex w-full items-center gap-2 rounded-md px-1 py-1 hover:bg-indigo-50 hover:text-indigo-600 dark:hover:bg-slate-800/70 dark:hover:text-indigo-300">
                                            Status
                                            <i class="fa-solid {{ $sortIcon('status') }} text-slate-400"></i>
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
                                @foreach ($orders as $order)
                                    <tr>
                                        <td class="px-6 py-4">
                                            <div class="font-mono font-semibold text-slate-800 dark:text-slate-100">{{ $order->order_number }}</div>
                                            <div class="text-xs text-slate-500 dark:text-slate-400">{{ $order->quantity }}x tiket</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="font-semibold text-slate-800 dark:text-slate-100">{{ $order->participant->name ?? 'N/A' }}</div>
                                            <div class="text-sm text-slate-500 dark:text-slate-400">{{ $order->event->title ?? 'N/A' }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-sm font-semibold text-slate-700 dark:text-slate-200">
                                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">
                                            @if($order->payment_method === 'transfer' && $order->bankAccount)
                                                <div>Transfer</div>
                                                <div class="text-xs text-slate-500">{{ $order->bankAccount->nama_bank }}</div>
                                            @else
                                                {{ ucfirst($order->payment_method) }}
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $order->status_color }}">
                                                {{ $order->status_label }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">{{ $order->created_at->format('d M Y H:i') }}</td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center justify-center gap-3 text-base">
                                                <a href="{{ route('event-orders.show', $order) }}" class="text-slate-500 transition hover:text-indigo-600" title="Detail">
                                                    <i class="fa-solid fa-eye"></i>
                                                </a>
                                                @if($order->status === 'pending')
                                                    <form action="{{ route('event-orders.approve', $order) }}" method="POST"
                                                          data-confirm-approve
                                                          data-confirm-title="Konfirmasi Pembayaran"
                                                          data-confirm-message="Apakah Anda yakin ingin mengkonfirmasi pembayaran order ini?">
                                                        @csrf
                                                        <button type="submit" class="text-slate-500 transition hover:text-emerald-600" title="Konfirmasi">
                                                            <i class="fa-solid fa-check"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                <form action="{{ route('event-orders.destroy', $order) }}" method="POST"
                                                      data-confirm-delete
                                                      data-confirm-title="Hapus Order"
                                                      data-confirm-message="Apakah Anda yakin ingin menghapus order ini?">
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
                            <form method="GET" action="{{ route('event-orders.index') }}" class="flex items-center gap-2" id="orders-per-page-form">
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
                                <input type="hidden" name="status" value="{{ $status ?? '' }}">
                                <input type="hidden" name="sort" value="{{ $currentSort }}">
                                <input type="hidden" name="direction" value="{{ $currentDirection }}">
                            </form>
                            <span>
                                Menampilkan
                                <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $orders->firstItem() }}</span>
                                sampai
                                <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $orders->lastItem() }}</span>
                                dari
                                <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $orders->total() }}</span>
                                order
                            </span>
                        </div>
                        <div>
                            {{ $orders->onEachSide(1)->links('vendor.pagination.tailwind-simple') }}
                        </div>
                    </div>
                @else
                    <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-10 text-center text-sm text-slate-500 dark:border-slate-700 dark:bg-slate-900/50 dark:text-slate-300">
                        @if (!empty($search))
                            Tidak ditemukan order dengan kata kunci <span class="font-semibold text-slate-700 dark:text-slate-200">"{{ $search }}"</span>.
                        @else
                            Belum ada order yang tersedia.
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
