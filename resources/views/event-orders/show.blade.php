<x-app-layout>
    <x-slot name="header">
        <div class="flex w-full flex-col gap-3">
            <h1 class="text-3xl font-semibold text-slate-900 dark:text-white">Detail Order Event</h1>
            <nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                <a href="{{ route('dashboard') }}" class="hover:text-indigo-600">Dashboard</a>
                <span>/</span>
                <a href="{{ route('event-orders.index') }}" class="hover:text-indigo-600">Order Events</a>
                <span>/</span>
                <span class="text-slate-700 dark:text-slate-200">{{ $eventOrder->order_number }}</span>
            </nav>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if(session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">{{ session('error') }}</div>
        @endif

        <div class="grid gap-6 lg:grid-cols-3">
            <!-- Order Details -->
            <div class="lg:col-span-2">
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-slate-900 dark:text-white">Informasi Order</h2>
                        <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium {{ $eventOrder->status_color }}">
                            {{ $eventOrder->status_label }}
                        </span>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="text-sm font-medium text-slate-600 dark:text-slate-300">Order Number</label>
                            <div class="font-mono font-semibold text-slate-900 dark:text-white">{{ $eventOrder->order_number }}</div>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-slate-600 dark:text-slate-300">Tanggal Order</label>
                            <div class="text-slate-900 dark:text-white">{{ $eventOrder->created_at->format('d M Y H:i') }}</div>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-slate-600 dark:text-slate-300">Peserta</label>
                            <div class="text-slate-900 dark:text-white">{{ $eventOrder->participant->name }}</div>
                            <div class="text-sm text-slate-500">{{ $eventOrder->participant->email }}</div>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-slate-600 dark:text-slate-300">Event</label>
                            <div class="text-slate-900 dark:text-white">{{ $eventOrder->event->title }}</div>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-slate-600 dark:text-slate-300">Jumlah Tiket</label>
                            <div class="text-slate-900 dark:text-white">{{ $eventOrder->quantity }}x</div>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-slate-600 dark:text-slate-300">Metode Pembayaran</label>
                            <div class="text-slate-900 dark:text-white">
                                @if($eventOrder->payment_method === 'transfer' && $eventOrder->bankAccount)
                                    Transfer - {{ $eventOrder->bankAccount->nama_bank }}
                                @else
                                    {{ ucfirst($eventOrder->payment_method) }}
                                @endif
                            </div>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-slate-600 dark:text-slate-300">Expired At</label>
                            <div class="text-slate-900 dark:text-white">{{ $eventOrder->expires_at->format('d M Y H:i') }}</div>
                        </div>

                        @if($eventOrder->paid_at)
                        <div>
                            <label class="text-sm font-medium text-slate-600 dark:text-slate-300">Dibayar Pada</label>
                            <div class="text-slate-900 dark:text-white">{{ $eventOrder->paid_at->format('d M Y H:i') }}</div>
                        </div>
                        @endif
                    </div>

                    @if($eventOrder->notes)
                    <div class="mt-6">
                        <label class="text-sm font-medium text-slate-600 dark:text-slate-300">Catatan</label>
                        <div class="mt-2 rounded-lg bg-slate-50 p-3 text-sm text-slate-700 dark:bg-slate-800 dark:text-slate-300">{{ $eventOrder->notes }}</div>
                    </div>
                    @endif

                    <!-- Payment Proof -->
                    @if($eventOrder->payment_proof_url)
                    <div class="mt-6">
                        <label class="text-sm font-medium text-slate-600 dark:text-slate-300">Bukti Pembayaran</label>
                        <div class="mt-2">
                            <img src="{{ $eventOrder->payment_proof_url }}" alt="Bukti Pembayaran" class="max-w-sm rounded-lg border border-slate-200 shadow-sm">
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Order Summary & Actions -->
            <div class="space-y-6">
                <!-- Order Summary -->
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Ringkasan Pembayaran</h3>

                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-600 dark:text-slate-300">Harga Tiket ({{ $eventOrder->quantity }}x)</span>
                            <span class="text-slate-900 dark:text-white">Rp {{ number_format($eventOrder->price * $eventOrder->quantity, 0, ',', '.') }}</span>
                        </div>

                        <div class="border-t border-slate-200 pt-3 dark:border-slate-700">
                            <div class="flex justify-between font-semibold">
                                <span class="text-slate-900 dark:text-white">Total</span>
                                <span class="text-slate-900 dark:text-white">Rp {{ number_format($eventOrder->total_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                @if($eventOrder->payment_method === 'transfer' && $eventOrder->bankAccount)
                <!-- Bank Account Details -->
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Detail Transfer</h3>

                    <div class="space-y-3 text-sm">
                        <div>
                            <span class="font-medium text-slate-600 dark:text-slate-300">Bank</span>
                            <div class="text-slate-900 dark:text-white">{{ $eventOrder->bankAccount->nama_bank }}</div>
                        </div>

                        <div>
                            <span class="font-medium text-slate-600 dark:text-slate-300">Pemilik Rekening</span>
                            <div class="text-slate-900 dark:text-white">{{ $eventOrder->bankAccount->nama }}</div>
                        </div>

                        <div>
                            <span class="font-medium text-slate-600 dark:text-slate-300">Nomor Rekening</span>
                            <div class="font-mono text-slate-900 dark:text-white">{{ $eventOrder->bankAccount->no_rek }}</div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Actions -->
                @if($eventOrder->status === 'pending')
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Aksi</h3>

                    <div class="space-y-3">
                        <form id="approve-order-form-{{ $eventOrder->id }}"
                              action="{{ route('event-orders.approve', $eventOrder) }}"
                              method="POST"
                              data-confirm-approve="approve-order-form-{{ $eventOrder->id }}"
                              data-confirm-title="Konfirmasi Pembayaran"
                              data-confirm-message="Apakah Anda yakin ingin mengkonfirmasi pembayaran order ini?"
                              data-confirm-button="Konfirmasi">
                            @csrf
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-emerald-700">
                                <i class="fa-solid fa-check"></i>
                                Konfirmasi Pembayaran
                            </button>
                        </form>

                        <button type="button"
                                onclick="showRejectModal()"
                                class="w-full inline-flex items-center justify-center gap-2 rounded-lg border border-rose-300 px-4 py-2 text-sm font-semibold text-rose-700 hover:bg-rose-50">
                            <i class="fa-solid fa-times"></i>
                            Batalkan Order
                        </button>
                    </div>
                </div>

                <!-- Reject Modal -->
                <div id="reject-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
                    <div class="max-w-md rounded-lg bg-white p-6 shadow-lg dark:bg-slate-800">
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Batalkan Order</h3>

                        <form action="{{ route('event-orders.reject', $eventOrder) }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label for="notes" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Alasan Pembatalan</label>
                                <textarea
                                    id="notes"
                                    name="notes"
                                    rows="3"
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-400 focus:outline-none focus:ring focus:ring-indigo-100 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200"
                                    placeholder="Masukkan alasan pembatalan..."
                                ></textarea>
                            </div>

                            <div class="flex gap-3">
                                <button type="button"
                                        onclick="hideRejectModal()"
                                        class="flex-1 rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                                    Batal
                                </button>
                                <button type="submit" class="flex-1 rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700">
                                    Batalkan Order
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                @endif

                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <a href="{{ route('event-orders.index') }}" class="w-full inline-flex items-center justify-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800/80">
                        <i class="fa-solid fa-arrow-left"></i>
                        Kembali ke Daftar Order
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showRejectModal() {
            document.getElementById('reject-modal').classList.remove('hidden');
            document.getElementById('reject-modal').classList.add('flex');
        }

        function hideRejectModal() {
            document.getElementById('reject-modal').classList.add('hidden');
            document.getElementById('reject-modal').classList.remove('flex');
        }
    </script>
</x-app-layout>
