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

    <!-- Filters -->
    <div class="mb-6 rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
        <form method="GET" action="{{ route('position-requests.index') }}" class="flex flex-col gap-4 sm:flex-row sm:items-end">
            <!-- Search -->
            <div class="flex-1">
                <label for="query" class="block text-sm font-medium text-gray-700">Cari Peserta atau Posisi</label>
                <input
                    type="text"
                    name="query"
                    id="query"
                    value="{{ $search }}"
                    placeholder="Nama peserta, email, atau posisi..."
                    class="mt-1 block w-full rounded-lg border border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm"
                />
            </div>

            <!-- Status Filter -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" id="status" class="mt-1 block rounded-lg border border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Menunggu</option>
                    <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Disetujui</option>
                    <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>

            <!-- Per Page -->
            <div>
                <label for="per_page" class="block text-sm font-medium text-gray-700">Per Halaman</label>
                <select name="per_page" id="per_page" class="mt-1 block rounded-lg border border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                    <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                </select>
            </div>

            <!-- Submit -->
            <button
                type="submit"
                class="rounded-lg bg-emerald-600 px-4 py-2 text-white transition hover:bg-emerald-700"
            >
                Cari
            </button>
            <a
                href="{{ route('position-requests.index') }}"
                class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-700 transition hover:bg-gray-50"
            >
                Reset
            </a>
        </form>
    </div>

    <!-- Table -->
    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        @if($positionRequests->count() > 0)
            <table class="w-full">
                <thead class="border-b border-gray-200 bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Peserta</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Posisi</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Status</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Tanggal</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-gray-900">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($positionRequests as $request)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm">
                                <div class="font-medium text-gray-900">{{ optional($request->participant)->name ?? '-' }}</div>
                                <div class="text-gray-500">{{ optional($request->participant)->email ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ optional($request->position)->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm">
                                @if($request->status === 'pending')
                                    <span class="inline-flex items-center rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-700">
                                        Menunggu
                                    </span>
                                @elseif($request->status === 'approved')
                                    <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                        Disetujui
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                        Ditolak
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $request->created_at->format('d M Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-right text-sm">
                                <div class="flex justify-end gap-2">
                                    <a
                                        href="{{ route('position-requests.show', $request) }}"
                                        class="text-emerald-600 transition hover:text-emerald-700"
                                    >
                                        Detail
                                    </a>
                                    @if($request->status === 'pending')
                                        <form method="POST" action="{{ route('position-requests.approve', $request) }}" class="inline">
                                            @csrf
                                            <button
                                                type="submit"
                                                class="text-green-600 transition hover:text-green-700"
                                                onclick="return confirm('Setujui pengajuan ini?')"
                                            >
                                                Setujui
                                            </button>
                                        </form>
                                        <button
                                            type="button"
                                            class="text-red-600 transition hover:text-red-700"
                                            onclick="showRejectModal({{ $request->id }})"
                                        >
                                            Tolak
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="px-6 py-12 text-center">
                <p class="text-gray-500">Tidak ada pengajuan posisi.</p>
            </div>
        @endif
    </div>

    <!-- Pagination -->
    @if($positionRequests->hasPages())
        <div class="mt-6">
            {{ $positionRequests->links() }}
        </div>
    @endif

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
