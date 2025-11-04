<x-app-layout>
    <x-slot name="header">
        <div class="flex w-full flex-col gap-3">
            <h1 class="text-3xl font-semibold text-slate-900 dark:text-white">Detail Pengajuan Posisi</h1>
            <nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                <a href="{{ route('dashboard') }}" class="hover:text-indigo-600 focus:text-indigo-600 dark:hover:text-indigo-300 dark:focus:text-indigo-300">Dashboard</a>
                <span>/</span>
                <a href="{{ route('participants.index') }}" class="hover:text-indigo-600 focus:text-indigo-600 dark:hover:text-indigo-300 dark:focus:text-indigo-300">Peserta</a>
                <span>/</span>
                <a href="{{ route('position-requests.index') }}" class="hover:text-indigo-600 focus:text-indigo-600 dark:hover:text-indigo-300 dark:focus:text-indigo-300">Pengajuan</a>
                <span>/</span>
                <span class="text-slate-700 dark:text-slate-200">Detail</span>
            </nav>
        </div>
    </x-slot>

    <div class="space-y-6">

    <div class="grid gap-6 lg:grid-cols-3">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <!-- Card: Request Info -->
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                <div class="mb-6 border-b border-gray-200 pb-6">
                    <h1 class="text-2xl font-bold text-gray-900">Pengajuan Posisi</h1>
                    <p class="mt-2 text-sm text-gray-500">Ref: #{{ $positionRequest->id }}</p>
                </div>

                <!-- Status -->
                <div class="mb-6">
                    <h3 class="text-sm font-semibold text-gray-700">Status</h3>
                    <div class="mt-3">
                        @if($positionRequest->status === 'pending')
                            <span class="inline-flex items-center rounded-full bg-yellow-100 px-4 py-2 text-sm font-semibold text-yellow-700">
                                ⏳ Menunggu Persetujuan
                            </span>
                        @elseif($positionRequest->status === 'approved')
                            <span class="inline-flex items-center rounded-full bg-green-100 px-4 py-2 text-sm font-semibold text-green-700">
                                ✓ Disetujui
                            </span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-red-100 px-4 py-2 text-sm font-semibold text-red-700">
                                ✕ Ditolak
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Timeline -->
                <div class="mb-6 space-y-4">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700">Diajukan</h3>
                        <p class="mt-1 text-sm text-gray-600">{{ $positionRequest->created_at->format('d F Y \p\u\k\u\l H:i') }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700">Diperbarui</h3>
                        <p class="mt-1 text-sm text-gray-600">{{ $positionRequest->updated_at->format('d F Y \p\u\k\u\l H:i') }}</p>
                    </div>
                </div>

                <!-- Notes -->
                @if($positionRequest->notes)
                    <div class="rounded-lg bg-blue-50 p-4">
                        <h3 class="text-sm font-semibold text-blue-900">Catatan Peserta</h3>
                        <p class="mt-2 text-sm text-blue-700">{{ $positionRequest->notes }}</p>
                    </div>
                @endif

                @if($positionRequest->admin_notes)
                    <div class="mt-4 rounded-lg bg-gray-50 p-4">
                        <h3 class="text-sm font-semibold text-gray-900">Catatan Admin</h3>
                        <p class="mt-2 text-sm text-gray-700">{{ $positionRequest->admin_notes }}</p>
                    </div>
                @endif
            </div>

            <!-- Card: Actions -->
            @if($positionRequest->status === 'pending')
                <div class="mt-6 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                    <h2 class="mb-4 text-lg font-semibold text-gray-900">Tindakan</h2>
                    <div class="flex gap-3">
                        <form method="POST" action="{{ route('position-requests.approve', $positionRequest) }}" class="flex-1">
                            @csrf
                            <button
                                type="submit"
                                class="w-full rounded-lg bg-green-600 px-4 py-3 text-white transition hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                                onclick="return confirm('Setujui pengajuan ini?')"
                            >
                                ✓ Setujui Pengajuan
                            </button>
                        </form>
                        <button
                            type="button"
                            class="flex-1 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-700 transition hover:bg-red-100"
                            onclick="showRejectForm()"
                        >
                            ✕ Tolak Pengajuan
                        </button>
                    </div>
                </div>

                <!-- Reject Form (Hidden) -->
                <div id="rejectForm" class="mt-6 hidden rounded-lg border border-red-200 bg-red-50 p-6">
                    <form method="POST" action="{{ route('position-requests.reject', $positionRequest) }}">
                        @csrf
                        <h3 class="text-lg font-semibold text-red-900">Tolak Pengajuan</h3>
                        <div class="mt-4">
                            <label for="notes" class="block text-sm font-medium text-red-900">Catatan Penolakan</label>
                            <textarea
                                name="notes"
                                id="notes"
                                rows="5"
                                placeholder="Jelaskan alasan penolakan atau saran untuk peserta..."
                                class="mt-2 block w-full rounded-lg border-red-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm"
                            ></textarea>
                        </div>
                        <div class="mt-4 flex gap-3">
                            <button
                                type="button"
                                class="flex-1 rounded-lg border border-red-300 bg-white px-4 py-2 text-red-700 transition hover:bg-red-50"
                                onclick="hideRejectForm()"
                            >
                                Batal
                            </button>
                            <button
                                type="submit"
                                class="flex-1 rounded-lg bg-red-600 px-4 py-2 text-white transition hover:bg-red-700"
                                onclick="return confirm('Tolak pengajuan ini?')"
                            >
                                Tolak Pengajuan
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Card: Peserta -->
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="mb-4 text-lg font-semibold text-gray-900">Peserta</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-500">Nama</p>
                        <p class="text-gray-900 font-semibold">{{ $positionRequest->participant->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Email</p>
                        <p class="text-gray-900">
                            <a href="mailto:{{ $positionRequest->participant->email }}" class="text-emerald-600 hover:text-emerald-700">
                                {{ $positionRequest->participant->email }}
                            </a>
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Generasi</p>
                        <p class="text-gray-900 font-semibold">{{ $positionRequest->participant->generation->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Posisi Saat Ini</p>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach($positionRequest->participant->positions as $position)
                                <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                                    {{ $position->name }}
                                </span>
                            @endforeach
                            @if($positionRequest->participant->positions->isEmpty())
                                <span class="text-sm text-gray-500">-</span>
                            @endif
                        </div>
                    </div>
                    <a
                        href="{{ route('participants.show', $positionRequest->participant) }}"
                        class="mt-4 block rounded-lg bg-emerald-50 px-4 py-2 text-center text-sm font-semibold text-emerald-600 transition hover:bg-emerald-100"
                    >
                        Lihat Profil Peserta
                    </a>
                </div>
            </div>

            <!-- Card: Posisi -->
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="mb-4 text-lg font-semibold text-gray-900">Posisi yang Diajukan</h3>
                <div>
                    <p class="text-sm text-gray-500">Nama Posisi</p>
                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ $positionRequest->position->name }}</p>
                </div>
                @if($positionRequest->position->description)
                    <div class="mt-4">
                        <p class="text-sm text-gray-500">Deskripsi</p>
                        <p class="mt-1 text-sm text-gray-700">{{ $positionRequest->position->description }}</p>
                    </div>
                @endif
                <a
                    href="{{ route('positions.edit', $positionRequest->position) }}"
                    class="mt-4 block rounded-lg bg-gray-50 px-4 py-2 text-center text-sm font-semibold text-gray-600 transition hover:bg-gray-100"
                >
                    Edit Posisi
                </a>
            </div>
        </div>
    </div>

<script>
    function showRejectForm() {
        document.getElementById('rejectForm').classList.remove('hidden');
        document.getElementById('notes').focus();
    }

    function hideRejectForm() {
        document.getElementById('rejectForm').classList.add('hidden');
        document.getElementById('notes').value = '';
    }
</script>
</x-app-layout>
