<x-app-layout>
    <x-slot name="header">
        <div class="flex w-full flex-col gap-3">
            <h1 class="text-3xl font-semibold text-slate-900 dark:text-white">Detail Event</h1>
            <nav class="flex flex-wrap items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                <a href="{{ route('dashboard') }}" class="hover:text-indigo-600">Dashboard</a>
                <span>/</span>
                <a href="{{ route('events.index') }}" class="hover:text-indigo-600">Events</a>
                <span>/</span>
                <span class="text-slate-700 dark:text-slate-200">{{ $event->title }}</span>
            </nav>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if(session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">{{ session('error') }}</div>
        @endif

        @php
            $latestOrder = $orders->first();
        @endphp

        <div class="grid gap-6 lg:grid-cols-[1.5fr_0.8fr]">
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex flex-col gap-6">
                    <div class="flex flex-col gap-5 sm:flex-row sm:items-start">
                        <div class="w-full max-w-[240px] shrink-0 overflow-hidden rounded-2xl border border-slate-200 bg-slate-50">
                            @if($event->photo_url)
                                <img src="{{ $event->photo_url }}" alt="Poster event" class="h-full w-full object-cover">
                            @else
                                <div class="flex h-48 w-full items-center justify-center text-sm text-slate-400">Belum ada foto event</div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-emerald-500">Event</p>
                            <div class="flex flex-col gap-2">
                                <h2 class="text-2xl font-semibold text-slate-900 dark:text-white">{{ $event->title }}</h2>
                                <p class="text-sm text-slate-600 dark:text-slate-300">{{ $event->subtitle ?? 'Subjudul belum ditambahkan.' }}</p>
                            </div>

                            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">Tanggal</p>
                                    <p class="text-base font-semibold text-slate-900 dark:text-white">{{ $event->event_date ? \Carbon\Carbon::parse($event->event_date)->translatedFormat('d F Y') : 'Belum ditentukan' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">Lokasi</p>
                                    <p class="text-base font-semibold text-slate-900 dark:text-white">{{ $event->location ?? 'Belum ditentukan' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">Harga Tiket</p>
                                    <p class="text-base font-semibold text-emerald-600">{{ $event->price ? 'Rp ' . number_format($event->price, 0, ',', '.') : 'Gratis' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">Total Peserta</p>
                                    <p class="text-base font-semibold text-slate-900 dark:text-white">{{ number_format($summary['completed'] + $summary['paid']) }} peserta terverifikasi</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-dashed border-slate-200 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">Deskripsi Event</p>
                        <div class="mt-3 text-sm leading-relaxed text-slate-600 dark:text-slate-300" data-description-wrapper data-lines="5">
                            @if($event->description)
                                <div data-description-content class="description-content description-content--clamped" style="--description-lines: 5;">
                                    {!! $event->description !!}
                                </div>
                                <button type="button" data-description-toggle class="mt-3 hidden cursor-pointer items-center gap-2 text-xs font-semibold text-emerald-600">
                                    <span data-description-toggle-label>Selengkapnya</span>
                                    <i data-description-toggle-icon class="fa-solid fa-chevron-down text-[10px]"></i>
                                </button>
                            @else
                                <span class="text-slate-400">Belum ada deskripsi event.</span>
                            @endif
                        </div>
                    </div>
                </div>
            </section>

            <section class="space-y-4">
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">Ringkasan Pesanan</p>

                    <div class="mt-4 rounded-2xl border border-indigo-100 bg-indigo-50/70 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-indigo-500">Pesanan</p>
                        <p class="mt-2 text-3xl font-semibold text-slate-900">{{ number_format($summary['total']) }}</p>
                        <p class="text-xs text-slate-500">Akumulasi tiket yang terdaftar</p>
                    </div>

                    @if($latestOrder)
                        <div class="mt-3 rounded-2xl border border-slate-100 bg-slate-50/70 p-4 text-sm text-slate-600 dark:border-slate-700 dark:bg-slate-800/60 dark:text-slate-200">
                            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">Pesanan Terbaru</p>
                            <p class="mt-2 font-semibold text-slate-900 dark:text-white">#{{ $latestOrder->order_number }}</p>
                            <p class="text-xs text-slate-500">{{ optional($latestOrder->participant)->name ?? 'Peserta tanpa nama' }} · {{ optional($latestOrder->created_at)->format('d M Y H:i') }}</p>
                        </div>
                    @endif

                    <ul class="mt-4 space-y-3 text-sm text-slate-600 dark:text-slate-300">
                        <li class="flex items-center justify-between">
                            <span>Menunggu</span>
                            <span class="font-semibold text-amber-600">{{ number_format($summary['pending']) }}</span>
                        </li>
                        <li class="flex items-center justify-between">
                            <span>Sudah Bayar</span>
                            <span class="font-semibold text-slate-900">{{ number_format($summary['paid']) }}</span>
                        </li>
                        <li class="flex items-center justify-between">
                            <span>Selesai</span>
                            <span class="font-semibold text-emerald-600">{{ number_format($summary['completed']) }}</span>
                        </li>
                    </ul>
                </div>

            </section>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-base font-semibold text-slate-900 dark:text-white">Peserta / Pemesan</p>
                    <p class="text-xs text-slate-500">Daftar pesanan tiket yang terkait dengan event ini.</p>
                </div>
                <a href="{{ route('events.index') }}" class="inline-flex items-center gap-2 rounded-full border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-600 transition hover:border-indigo-200 hover:text-indigo-600">
                    <i class="fa-solid fa-arrow-left"></i>
                    Kembali ke Events
                </a>
            </div>

            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm text-slate-600 dark:divide-slate-700 dark:text-slate-300">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:bg-slate-800 dark:text-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-left">Pesanan &amp; Peserta</th>
                            <th class="px-4 py-3 text-left">Metode</th>
                            <th class="px-4 py-3 text-left">Jumlah</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-left">Check-in</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($orders as $order)
                            @php
                                $participant = $order->participant;
                                $participantName = trim($participant?->name ?? '');
                                $nameParts = $participantName !== '' ? preg_split('/\s+/', $participantName) : [];
                                $initials = '';
                                if (!empty($nameParts)) {
                                    foreach ($nameParts as $part) {
                                        if ($part === '') {
                                            continue;
                                        }
                                        $initials .= mb_strtoupper(mb_substr($part, 0, 1));
                                        if (mb_strlen($initials) >= 2) {
                                            break;
                                        }
                                    }
                                }
                                $photoPath = $participant?->photo ?? null;
                                $photoUrl = null;
                                if ($photoPath) {
                                    $photoUrl = \Illuminate\Support\Str::startsWith($photoPath, ['http://', 'https://'])
                                        ? $photoPath
                                        : asset(ltrim($photoPath, '/'));
                                }
                            @endphp
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="flex items-start gap-3">
                                        <div class="h-12 w-12 shrink-0 overflow-hidden rounded-full border border-slate-200 bg-slate-100 text-center">
                                            @if($photoUrl)
                                                <img src="{{ $photoUrl }}" alt="Foto {{ $participantName }}" class="h-full w-full object-cover">
                                            @else
                                                <span class="inline-flex h-full w-full items-center justify-center text-xs font-semibold uppercase text-emerald-600">{{ $initials !== '' ? $initials : 'NA' }}</span>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-900 dark:text-white">{{ $participantName !== '' ? $participantName : '—' }}</p>
                                            <p class="text-xs text-slate-500">{{ $participant?->email ?? '-' }}</p>
                                            <p class="mt-1 text-[11px] text-slate-400">#{{ $order->order_number }} • {{ optional($order->created_at)->format('d M Y H:i') }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 capitalize">{{ $order->payment_method }}</td>
                                <td class="px-4 py-3">{{ $order->quantity }} tiket</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold {{ $order->status_color }}">
                                        {{ $order->status_label }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    @if($order->checked_in_at)
                                        <span class="font-semibold text-emerald-600">Sudah check-in</span>
                                        <p class="text-slate-500">{{ optional($order->checked_in_at)->format('d M Y H:i') }}</p>
                                    @else
                                        <span class="text-slate-400">Belum</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('event-orders.show', $order) }}" class="inline-flex items-center rounded-full border border-slate-200 px-3 py-1 text-xs font-semibold text-slate-600 transition hover:border-indigo-300 hover:text-indigo-600">
                                            Detail Order
                                        </a>
                                        @if($order->status === 'paid')
                                            <form id="check-in-form-{{ $order->id }}" action="{{ route('event-orders.check-in', $order) }}" method="POST"
                                                  data-confirm-approve="check-in-form-{{ $order->id }}"
                                                  data-confirm-title="Konfirmasi Check-in"
                                                  data-confirm-message="Tandai tiket #{{ $order->order_number }} sudah digunakan?"
                                                  data-confirm-button="Konfirmasi"
                                                  data-confirm-color="#10B981">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center rounded-full border border-emerald-300 px-3 py-1 text-xs font-semibold text-emerald-600 transition hover:bg-emerald-50">
                                                    Check-in
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-sm text-slate-500">Belum ada peserta yang membeli tiket.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @once
        <style>
            .description-content {
                position: relative;
            }

            .description-content--clamped {
                display: -webkit-box;
                -webkit-box-orient: vertical;
                -webkit-line-clamp: var(--description-lines, 5);
                overflow: hidden;
            }

            .description-content p:last-child {
                margin-bottom: 0;
            }
        </style>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const instances = [];

                document.querySelectorAll('[data-description-wrapper]').forEach((wrapper) => {
                    const content = wrapper.querySelector('[data-description-content]');
                    const toggle = wrapper.querySelector('[data-description-toggle]');
                    const label = wrapper.querySelector('[data-description-toggle-label]');
                    const icon = wrapper.querySelector('[data-description-toggle-icon]');

                    if (!content || !toggle || !label) {
                        return;
                    }

                    const configuredLines = parseInt(wrapper.dataset.lines || '5', 10);
                    if (!Number.isNaN(configuredLines)) {
                        content.style.setProperty('--description-lines', configuredLines);
                    }

                    let expanded = false;

                    const setState = (nextState) => {
                        expanded = nextState;
                        if (expanded) {
                            content.classList.remove('description-content--clamped');
                            label.textContent = 'Sembunyikan';
                            if (icon) {
                                icon.classList.remove('fa-chevron-down');
                                icon.classList.add('fa-chevron-up');
                            }
                        } else {
                            content.classList.add('description-content--clamped');
                            label.textContent = 'Selengkapnya';
                            if (icon) {
                                icon.classList.add('fa-chevron-down');
                                icon.classList.remove('fa-chevron-up');
                            }
                        }
                    };

                    const hasOverflow = () => {
                        const wasClamped = content.classList.contains('description-content--clamped');
                        if (!wasClamped) {
                            content.classList.add('description-content--clamped');
                        }

                        const overflowing = content.scrollHeight - content.clientHeight > 4;

                        if (!wasClamped && expanded) {
                            content.classList.remove('description-content--clamped');
                        }

                        return overflowing;
                    };

                    const evaluateOverflow = () => {
                        requestAnimationFrame(() => {
                            const overflowing = hasOverflow();
                            if (overflowing) {
                                toggle.classList.remove('hidden');
                            } else {
                                toggle.classList.add('hidden');
                                if (expanded) {
                                    setState(false);
                                }
                            }
                        });
                    };

                    toggle.addEventListener('click', () => {
                        setState(!expanded);
                        if (!expanded) {
                            evaluateOverflow();
                        }
                    });

                    setState(false);
                    evaluateOverflow();

                    instances.push({ evaluateOverflow });
                });

                if (instances.length) {
                    let resizeTimer;
                    window.addEventListener('resize', () => {
                        clearTimeout(resizeTimer);
                        resizeTimer = setTimeout(() => {
                            instances.forEach((instance) => instance.evaluateOverflow());
                        }, 200);
                    });
                }
            });
        </script>
    @endonce
</x-app-layout>
