<x-app-layout>
    <x-slot name="header">
        <div class="flex w-full flex-col gap-3">
            <h1 class="text-3xl font-semibold text-slate-900 dark:text-white">Pengajuan Posisi</h1>
            <nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                <a href="{{ route('dashboard') }}" class="hover:text-indigo-600 focus:text-indigo-600 dark:hover:text-indigo-300 dark:focus:text-indigo-300">Dashboard</a>
                <span>/</span>
                <a href="{{ route('participants.index') }}" class="hover:text-indigo-600 focus:text-indigo-600 dark:hover:text-indigo-300 dark:focus:text-indigo-300">Peserta</a>
                <span>/</span>
                <span class="text-slate-700 dark:text-slate-200">Pengajuan</span>
            </nav>
        </div>
    </x-slot>

    <div class="space-y-6">

    <!-- Stats Cards -->
    <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Total Pengajuan</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ $stats['total'] }}</p>
        </div>
        <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-6 shadow-sm">
            <p class="text-sm font-medium text-yellow-600">Menunggu</p>
            <p class="mt-2 text-3xl font-bold text-yellow-900">{{ $stats['pending'] }}</p>
        </div>
        <div class="rounded-lg border border-green-200 bg-green-50 p-6 shadow-sm">
            <p class="text-sm font-medium text-green-600">Disetujui</p>
            <p class="mt-2 text-3xl font-bold text-green-900">{{ $stats['approved'] }}</p>
        </div>
        <div class="rounded-lg border border-red-200 bg-red-50 p-6 shadow-sm">
            <p class="text-sm font-medium text-red-600">Ditolak</p>
            <p class="mt-2 text-3xl font-bold text-red-900">{{ $stats['rejected'] }}</p>
        </div>
    </div>

    <!-- Listing Card (search + table) - follow News index pattern -->
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="flex items-center justify-between gap-4">
            <form method="GET" action="{{ route('position-requests.index') }}" class="relative w-full max-w-xs">
                <label for="pr-search" class="sr-only">Cari pengajuan</label>
                <input type="search" id="pr-search" name="query" value="{{ $search ?? '' }}" class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pl-11 pr-4 text-sm text-slate-700 shadow-sm transition focus:border-indigo-400 focus:bg-white focus:outline-none focus:ring focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:border-indigo-500" placeholder="Cari peserta atau posisi...">
                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
            </form>

            <div class="flex items-center gap-3">
                <!-- Placeholder for actions, e.g., export or create if needed -->
                <a href="{{ route('position-requests.index') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-700 transition hover:bg-gray-50">Reset</a>
            </div>
        </div>

        @if(session('success'))
            <div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
        @endif

        <div class="mt-6">
            @if($positionRequests->count() > 0)
                <div class="overflow-hidden rounded-xl border border-slate-200 dark:border-slate-700">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                        <thead class="bg-slate-50 dark:bg-slate-800/60">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">Peserta</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">Posisi</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white dark:divide-slate-800 dark:bg-slate-900">
                            @foreach($positionRequests as $request)
                                <tr>
                                    <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">
                                        <div class="font-medium text-slate-700 dark:text-slate-200">{{ optional($request->participant)->name ?? '-' }}</div>
                                        <div class="text-slate-500 dark:text-slate-400">{{ optional($request->participant)->email ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">{{ optional($request->position)->name ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm">
                                        @if($request->status === 'pending')
                                            <span class="inline-flex items-center rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-700">Menunggu</span>
                                        @elseif($request->status === 'approved')
                                            <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">Disetujui</span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">Ditolak</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">{{ optional($request->created_at)->format('d M Y H:i') }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-start gap-2">
                                            <a href="{{ route('position-requests.show', $request) }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:border-indigo-400 hover:text-indigo-600" title="Detail">
                                                <i class="fa-solid fa-eye text-xs"></i>
                                                Detail
                                            </a>
                                            @if($request->status === 'pending')
                                                <form id="approve-form-{{ $request->id }}" method="POST" action="{{ route('position-requests.approve', $request) }}" class="inline">
                                                    @csrf
                                                    <button type="button" data-confirm-approve="approve-form-{{ $request->id }}" data-participant-name="{{ optional($request->participant)->name ?? '' }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:border-indigo-400 hover:text-emerald-600">
                                                        <i class="fa-solid fa-check text-xs"></i>
                                                        Setujui
                                                    </button>
                                                </form>
                                                <button type="button" data-confirm-reject="{{ $request->id }}" data-participant-name="{{ optional($request->participant)->name ?? '' }}" class="inline-flex items-center gap-2 rounded-lg border border-rose-200 px-3 py-2 text-xs font-semibold text-rose-600 transition hover:bg-rose-50">
                                                    <i class="fa-solid fa-times text-xs"></i>
                                                    Tolak
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex flex-wrap items-center gap-3 text-sm text-slate-500 dark:text-slate-400">
                        <form method="GET" action="{{ route('position-requests.index') }}" class="flex items-center gap-2" id="pr-per-page-form">
                            <span>Tampilkan</span>
                            <select
                                name="per_page"
                                class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 transition hover:border-indigo-400 focus:border-indigo-400 focus:outline-none focus:ring focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                                onchange="this.form.submit()"
                            >
                                @foreach ([10, 25, 50, 100] as $option)
                                    <option value="{{ $option }}" @selected($perPage === $option)>{{ $option }}</option>
                                @endforeach
                            </select>
                            <span>per halaman</span>
                            <input type="hidden" name="query" value="{{ $search ?? '' }}">
                        </form>
                        <span>
                            Menampilkan
                            <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $positionRequests->firstItem() }}</span>
                            sampai
                            <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $positionRequests->lastItem() }}</span>
                            dari
                            <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $positionRequests->total() }}</span>
                            pengajuan
                        </span>
                    </div>
                    <div>
                        {{ $positionRequests->onEachSide(1)->links('vendor.pagination.tailwind-simple') }}
                    </div>
                </div>
            @else
                <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-10 text-center text-sm text-slate-500 dark:border-slate-700 dark:bg-slate-900/50 dark:text-slate-300">
                    @if (!empty($search))
                        Tidak ditemukan pengajuan dengan kata kunci <span class="font-semibold text-slate-700 dark:text-slate-200">"{{ $search }}"</span>.
                    @else
                        Tidak ada pengajuan posisi saat ini.
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Reject Modal -->
    <div
        id="rejectModal"
        class="fixed inset-0 z-50 items-center justify-center bg-black/50 px-4"
        style="display: none;"
    >
    <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-lg">
        <h3 class="text-lg font-semibold text-gray-900">Tolak Pengajuan</h3>
        <form id="rejectForm" method="POST" class="mt-4">
            @csrf
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700">Catatan (Opsional)</label>
                <textarea
                    name="notes"
                    id="notes"
                    rows="4"
                    placeholder="Jelaskan alasan penolakan..."
                    class="mt-1 block w-full rounded-lg border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm"
                ></textarea>
            </div>
            <div class="mt-6 flex gap-3">
                <button
                    type="button"
                    onclick="hideRejectModal()"
                    class="flex-1 rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-700 transition hover:bg-gray-50"
                >
                    Batal
                </button>
                <button
                    type="submit"
                    class="flex-1 rounded-lg bg-red-600 px-4 py-2 text-white transition hover:bg-red-700"
                >
                    Tolak Pengajuan
                </button>
            </div>
        </form>
    </div>
    </div>

<script>
    let currentRequestId = null;

    function showRejectModal(requestId) {
        currentRequestId = requestId;
        const form = document.getElementById('rejectForm');
        form.action = `/position-requests/${requestId}/reject`;
        document.getElementById('rejectModal').style.display = 'flex';
    }

    function hideRejectModal() {
        document.getElementById('rejectModal').style.display = 'none';
        document.getElementById('notes').value = '';
        currentRequestId = null;
    }
</script>
</x-app-layout>
