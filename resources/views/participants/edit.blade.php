<x-app-layout>
    <x-slot name="header">
        <div class="flex w-full flex-col gap-3">
            <h1 class="text-3xl font-semibold text-slate-900 dark:text-white">Edit Peserta</h1>
            <nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                <a href="{{ route('dashboard') }}" class="hover:text-indigo-600 focus:text-indigo-600 dark:hover:text-indigo-300 dark:focus:text-indigo-300">Dashboard</a>
                <span>/</span>
                <a href="{{ route('participants.index') }}" class="hover:text-indigo-600 focus:text-indigo-600 dark:hover:text-indigo-300 dark:focus:text-indigo-300">Peserta</a>
                <span>/</span>
                <span class="text-slate-700 dark:text-slate-200">{{ $participant->name }}</span>
            </nav>
        </div>
    </x-slot>

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <h2 class="text-xl font-semibold text-slate-900 dark:text-white">Perbarui Data Peserta</h2>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Sesuaikan informasi peserta di bawah ini.</p>

        @if ($errors->any())
            <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-900/40 dark:bg-rose-900/20 dark:text-rose-200">
                <ul class="list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('participants.update', $participant) }}" method="POST" class="mt-6 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Nama</label>
                        <input type="text" name="name" value="{{ old('name', $participant->name) }}" required class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm text-slate-900 shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:focus:border-indigo-400">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Username</label>
                        <input type="text" name="username" value="{{ old('username', $participant->username) }}" required class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm text-slate-900 shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:focus:border-indigo-400">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Email</label>
                        <input type="email" name="email" value="{{ old('email', $participant->email) }}" required class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm text-slate-900 shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:focus:border-indigo-400">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Nomor HP</label>
                        <input type="text" name="no_hp" value="{{ old('no_hp', $participant->no_hp) }}" required class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm text-slate-900 shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:focus:border-indigo-400">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Tanggal Lahir</label>
                        <input type="date" name="birthday" value="{{ old('birthday', optional($participant->birthday)->format('Y-m-d')) }}" class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm text-slate-900 shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:focus:border-indigo-400">
                    </div>
                </div>
                <div class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Asal Sekolah</label>
                        <input type="text" name="from_school" value="{{ old('from_school', $participant->from_school) }}" class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm text-slate-900 shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:focus:border-indigo-400">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-300">URL Foto (opsional)</label>
                        <input type="text" name="photo" value="{{ old('photo', $participant->photo) }}" class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm text-slate-900 shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:focus:border-indigo-400">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Generasi</label>
                        <select name="generations_id" class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm text-slate-900 shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:focus:border-indigo-400">
                            <option value="">-- Pilih generasi --</option>
                            @foreach ($generations as $generation)
                                <option value="{{ $generation->id }}" @selected(old('generations_id', $participant->generations_id) === $generation->id)>
                                    {{ $generation->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Posisi</label>
                        @php
                            $selectedPositions = collect(old('positions', $participant->positions->pluck('id')->all()));
                            $order = ['tamu','guest','anggota','pengurus wilayah','pengurus pusat'];
                            $orderedPositions = $positions->sortBy(function($p) use ($order){
                                $name = strtolower($p->name);
                                $index = array_search($name, $order);
                                return $index === false ? 999 : $index;
                            })->values();
                            $initialSelected = $selectedPositions->first();
                            $displayName = $orderedPositions->firstWhere('id', $initialSelected)?->name ?? null;
                        @endphp

                        <!-- Visible trigger button that opens modal -->
                        <button type="button" id="positions-trigger" class="w-full text-left rounded-lg border border-slate-300 px-4 py-3 text-sm text-slate-900 shadow-sm transition hover:border-indigo-400 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
                            <span id="positions-selected-text">
                                @if(!$displayName)
                                    Pilih posisi...
                                @else
                                    {{ $displayName }}
                                @endif
                            </span>
                            <i class="fa-solid fa-chevron-down float-right mt-1 text-xs text-slate-400"></i>
                        </button>

                        <!-- Hidden native select to keep server-side binding on submit -->
                        <select name="positions[]" id="positions-hidden-select" multiple class="hidden">
                            @foreach ($orderedPositions as $position)
                                <option value="{{ $position->id }}" @selected($selectedPositions->contains($position->id))>
                                    {{ $position->name }}
                                </option>
                            @endforeach
                        </select>

                        <p class="text-xs text-slate-500 dark:text-slate-400">Klik kolom untuk memilih posisi (hanya satu, pilih posisi tertinggi).</p>

                        <!-- Modal -->
                        <div id="positions-modal" class="fixed inset-0 z-50 hidden items-center justify-center px-4">
                            <div class="fixed inset-0 bg-black/40" data-close-modal></div>
                            <div class="relative w-full max-w-2xl overflow-hidden rounded-2xl bg-white shadow-lg dark:bg-slate-900">
                                <div class="border-b px-6 py-4">
                                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Pilih Posisi</h3>
                                </div>
                                <div class="p-6">
                                    <div class="mb-3">
                                        <p class="text-sm text-slate-600 dark:text-slate-300">Catatan: Pilih satu posisi tertinggi. Urutan: Tamu &gt; Anggota &gt; Pengurus Wilayah &gt; Pengurus Pusat. Memilih posisi yang lebih tinggi berarti posisi sebelumnya akan terlewati.</p>
                                    </div>
                                    <div class="mb-4 grid grid-cols-1 gap-2 sm:grid-cols-2">
                                        <div class="relative">
                                            <input id="positions-modal-search" type="search" placeholder="Cari posisi" class="w-full rounded-lg border border-slate-200 px-4 py-2 text-sm text-slate-700 shadow-sm focus:border-indigo-500 focus:outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
                                        </div>
                                    </div>

                                    <div id="positions-list" class="max-h-64 overflow-auto rounded-md border border-slate-200 dark:border-slate-800">
                                        @foreach ($orderedPositions as $position)
                                            <label class="flex items-center gap-3 border-b last:border-b-0 px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-800 cursor-pointer positions-item" data-name="{{ strtolower($position->name) }}">
                                                <input type="radio" name="positions_radio" class="positions-radio" data-position-id="{{ $position->id }}" data-position-name="{{ $position->name }}" @checked($initialSelected == $position->id)>
                                                <span class="text-sm text-slate-700 dark:text-slate-100">{{ $position->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="flex items-center justify-end gap-3 border-t px-6 py-4">
                                    <button type="button" data-close-modal class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300">Batal</button>
                                    <button type="button" id="positions-save-btn" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700">
                                        <i class="fa-solid fa-check text-xs"></i>
                                        Simpan Posisi
                                    </button>
                                </div>
                            </div>
                        </div>

                        <script>
                            (function(){
                                const trigger = document.getElementById('positions-trigger');
                                const modal = document.getElementById('positions-modal');
                                const closeButtons = modal ? modal.querySelectorAll('[data-close-modal]') : [];
                                const searchInput = document.getElementById('positions-modal-search');
                                const items = modal ? modal.querySelectorAll('.positions-item') : [];
                                const saveBtn = document.getElementById('positions-save-btn');
                                const hiddenSelect = document.getElementById('positions-hidden-select');
                                const selectedText = document.getElementById('positions-selected-text');

                                function openModal(){
                                    modal.classList.remove('hidden');
                                    modal.classList.add('flex');
                                    searchInput?.focus();
                                }

                                function closeModal(){
                                    modal.classList.add('hidden');
                                    modal.classList.remove('flex');
                                }

                                if(trigger){
                                    trigger.addEventListener('click', openModal);
                                }

                                closeButtons.forEach(btn => btn.addEventListener('click', closeModal));

                                if(searchInput){
                                    searchInput.addEventListener('input', function(e){
                                        const q = (e.target.value || '').toLowerCase();
                                        items.forEach(item => {
                                            const name = item.dataset.name || '';
                                            item.style.display = name.indexOf(q) === -1 ? 'none' : '';
                                        });
                                    });
                                }

                                // Save selection back to hidden select (single choice)
                                if(saveBtn){
                                    saveBtn.addEventListener('click', function(){
                                        const checked = modal.querySelector('.positions-radio:checked');
                                        // clear all selections in hidden select
                                        Array.from(hiddenSelect.options).forEach(opt => opt.selected = false);
                                        let name = '';
                                        if(checked){
                                            const id = checked.dataset.positionId;
                                            name = checked.dataset.positionName;
                                            const option = hiddenSelect.querySelector(`option[value="${id}"]`);
                                            if(option) option.selected = true;
                                        }

                                        // update visible text
                                        if(!name){
                                            selectedText.innerText = 'Pilih posisi...';
                                        } else {
                                            selectedText.innerText = name;
                                        }

                                        closeModal();
                                    });
                                }
                            })();
                        </script>
                    </div>
                    <div class="grid gap-6 md:grid-cols-2">
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Password Baru</label>
                            <input type="password" name="password" class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm text-slate-900 shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:focus:border-indigo-400">
                            <p class="text-xs text-slate-500 dark:text-slate-400">Kosongkan jika tidak ingin mengubah password.</p>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm text-slate-900 shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:focus:border-indigo-400">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('participants.index') }}" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:text-slate-800 dark:border-slate-700 dark:text-slate-300 dark:hover:border-slate-600 dark:hover:text-slate-100">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700">
                    <i class="fa-solid fa-check text-xs"></i>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
