<x-app-layout>
    <x-slot name="header">
        <div class="flex w-full flex-col gap-3">
            <h1 class="text-3xl font-semibold text-slate-900 dark:text-white">Detail Kegiatan</h1>
            <nav class="flex flex-wrap items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                <a href="{{ route('dashboard') }}" class="hover:text-indigo-600">Dashboard</a>
                <span>/</span>
                <a href="{{ route('activities.index') }}" class="hover:text-indigo-600">Kegiatan</a>
                <span>/</span>
                <span class="text-slate-700 dark:text-slate-200">{{ $activity->name }}</span>
            </nav>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if(session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
        @endif

        <div class="grid gap-6 lg:grid-cols-[1.4fr_0.8fr]">
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900">
                <div class="flex flex-col gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-emerald-500">Kegiatan</p>
                        <h2 class="text-2xl font-semibold text-slate-900 dark:text-white">{{ $activity->name }}</h2>
                        <p class="text-sm text-slate-500">{{ $activity->location ?: 'Lokasi belum ditentukan' }}</p>
                    </div>

                    <dl class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">Waktu</dt>
                            <dd class="text-base font-semibold text-slate-900 dark:text-white">{{ optional($activity->datetime)->translatedFormat('d F Y H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">Status</dt>
                            <dd>
                                @php
                                    $statusMap = [
                                        'scheduled' => 'bg-slate-100 text-slate-700',
                                        'completed' => 'bg-emerald-100 text-emerald-700',
                                        'cancelled' => 'bg-rose-100 text-rose-700',
                                    ];
                                @endphp
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusMap[$activity->status] ?? 'bg-slate-100 text-slate-700' }}">
                                    {{ ucfirst($activity->status) }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">Target</dt>
                            <dd class="text-sm text-slate-600">
                                @if($activity->target_scope === 'all')
                                    Semua peserta
                                @else
                                    {{ $activity->positions->pluck('name')->join(', ') }}
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">Link Presensi</dt>
                            <dd class="text-sm text-indigo-600">
                                {{ url('/participants/activities/'.$activity->id) }}
                            </dd>
                        </div>
                    </dl>

                    <div class="rounded-2xl border border-dashed border-slate-200 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">Deskripsi</p>
                        <p class="mt-3 text-sm leading-relaxed text-slate-600 dark:text-slate-300">{{ $activity->desc ?: 'Belum ada deskripsi kegiatan.' }}</p>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Statistik Presensi</h3>
                <ul class="mt-4 space-y-3 text-sm text-slate-600 dark:text-slate-300">
                    <li class="flex items-center justify-between">
                        <span>Total laporan</span>
                        <span class="font-semibold text-slate-900 dark:text-white">{{ $reports->count() }}</span>
                    </li>
                    <li class="flex items-center justify-between">
                        <span>Hadir</span>
                        <span class="font-semibold text-emerald-600">{{ $reports->where('status', 'present')->count() }}</span>
                    </li>
                    <li class="flex items-center justify-between">
                        <span>Izin</span>
                        <span class="font-semibold text-amber-600">{{ $reports->where('status', 'excused')->count() }}</span>
                    </li>
                    <li class="flex items-center justify-between">
                        <span>Absen</span>
                        <span class="font-semibold text-rose-600">{{ $reports->where('status', 'absent')->count() }}</span>
                    </li>
                </ul>
                <div class="mt-6 flex flex-col gap-3">
                    <a href="{{ route('activities.edit', $activity) }}" class="rounded-xl border border-slate-200 px-4 py-2 text-center text-sm font-semibold text-slate-600 transition hover:border-indigo-200 hover:text-indigo-600">Edit Kegiatan</a>
                </div>
            </section>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-base font-semibold text-slate-900 dark:text-white">Laporan Presensi</p>
                    <p class="text-xs text-slate-500">Peserta yang sudah melakukan presensi.</p>
                </div>
            </div>

            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm text-slate-600 dark:divide-slate-700 dark:text-slate-300">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:bg-slate-800 dark:text-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-left">Peserta</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-left">Waktu Presensi</th>
                            <th class="px-4 py-3 text-left">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($reports as $report)
                            <tr>
                                <td class="px-4 py-3">
                                    <p class="font-semibold text-slate-900 dark:text-white">{{ $report->participant->name ?? '-' }}</p>
                                    <p class="text-xs text-slate-500">{{ $report->participant->email ?? '-' }}</p>
                                </td>
                                <td class="px-4 py-3 capitalize">{{ $report->status }}</td>
                                <td class="px-4 py-3">{{ optional($report->checked_in_at)->translatedFormat('d F Y H:i') ?: '—' }}</td>
                                <td class="px-4 py-3 text-slate-500">{{ $report->notes ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-sm text-slate-500">Belum ada laporan presensi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
