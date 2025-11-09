<x-app-layout>
    <x-slot name="header">
        <div class="flex w-full flex-col gap-3">
            <h1 class="text-3xl font-semibold text-slate-900 dark:text-white">Kegiatan & Rapat</h1>
            <nav class="flex flex-wrap items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                <a href="{{ route('dashboard') }}" class="hover:text-indigo-600">Dashboard</a>
                <span>/</span>
                <span class="text-slate-700 dark:text-slate-200">Kegiatan & Rapat</span>
            </nav>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-900">
                <p class="text-xs font-medium uppercase tracking-[0.2em] text-slate-400">Total</p>
                <p class="mt-2 text-3xl font-semibold text-slate-900 dark:text-white">{{ number_format($stats['total']) }}</p>
                <p class="text-xs text-slate-500">Semua kegiatan</p>
            </div>
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50/80 p-4 shadow-sm dark:border-emerald-500/40 dark:bg-emerald-500/10">
                <p class="text-xs font-medium uppercase tracking-[0.2em] text-emerald-500">Terjadwal</p>
                <p class="mt-2 text-3xl font-semibold text-emerald-600">{{ number_format($stats['scheduled']) }}</p>
                <p class="text-xs text-emerald-600">Akan berlangsung</p>
            </div>
            <div class="rounded-2xl border border-sky-200 bg-sky-50 p-4 shadow-sm dark:border-sky-500/40 dark:bg-sky-500/10">
                <p class="text-xs font-medium uppercase tracking-[0.2em] text-sky-500">Selesai</p>
                <p class="mt-2 text-3xl font-semibold text-sky-600">{{ number_format($stats['completed']) }}</p>
                <p class="text-xs text-sky-600">Sudah terlaksana</p>
            </div>
            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 shadow-sm dark:border-rose-500/40 dark:bg-rose-500/10">
                <p class="text-xs font-medium uppercase tracking-[0.2em] text-rose-500">Dibatalkan</p>
                <p class="mt-2 text-3xl font-semibold text-rose-600">{{ number_format($stats['cancelled']) }}</p>
                <p class="text-xs text-rose-600">Tidak jadi berlangsung</p>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <form method="GET" class="flex flex-wrap gap-3">
                    <input
                        type="search"
                        name="query"
                        value="{{ $search }}"
                        placeholder="Cari kegiatan..."
                        class="w-64 rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-indigo-400 focus:bg-white focus:outline-none focus:ring focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100"
                    >
                    <select
                        name="status"
                        class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-indigo-400 focus:outline-none focus:ring focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100"
                        onchange="this.form.submit()"
                    >
                        <option value="">Semua Status</option>
                        <option value="scheduled" @selected($status === 'scheduled')>Terjadwal</option>
                        <option value="completed" @selected($status === 'completed')>Selesai</option>
                        <option value="cancelled" @selected($status === 'cancelled')>Dibatalkan</option>
                    </select>
                </form>
                <a
                    href="{{ route('activities.create') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700"
                >
                    <x-icon name="plus" class="h-4 w-4" />
                    Tambah Kegiatan
                </a>
            </div>

            <div class="mt-6 overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm text-slate-600 dark:divide-slate-700 dark:text-slate-200">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:bg-slate-800 dark:text-slate-300">
                        <tr>
                            <th class="px-4 py-3 text-left">Kegiatan</th>
                            <th class="px-4 py-3 text-left">Tanggal</th>
                            <th class="px-4 py-3 text-left">Target</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($activities as $activity)
                            <tr>
                                <td class="px-4 py-4">
                                    <p class="font-semibold text-slate-900 dark:text-white">{{ $activity->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $activity->location ?: 'Lokasi belum ditentukan' }}</p>
                                </td>
                                <td class="px-4 py-4">{{ optional($activity->datetime)->translatedFormat('d F Y H:i') }}</td>
                                <td class="px-4 py-4">
                                    @if($activity->target_scope === 'all')
                                        <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">Semua peserta</span>
                                    @else
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($activity->positions as $position)
                                                <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-600">{{ $position->name }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    @php
                                        $statusClasses = [
                                            'scheduled' => 'bg-slate-100 text-slate-700',
                                            'completed' => 'bg-emerald-100 text-emerald-700',
                                            'cancelled' => 'bg-rose-100 text-rose-700',
                                        ];
                                    @endphp
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses[$activity->status] ?? 'bg-slate-100 text-slate-600' }}">
                                        {{ ucfirst($activity->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('activities.show', $activity) }}" class="text-indigo-600 hover:text-indigo-800">Detail</a>
                                        <a href="{{ route('activities.edit', $activity) }}" class="text-slate-500 hover:text-slate-700">Edit</a>
                                        <form action="{{ route('activities.destroy', $activity) }}" method="POST" onsubmit="return confirm('Hapus kegiatan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-500 hover:text-rose-600">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">Belum ada kegiatan terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $activities->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
