<x-app-layout>
    <x-slot name="header">
        <div class="flex w-full flex-col gap-3">
            <h1 class="text-3xl font-semibold text-slate-900 dark:text-white">Kelola Visi &amp; Misi</h1>
            <nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                <a href="{{ route('dashboard') }}" class="hover:text-indigo-600">Dashboard</a>
                <span>/</span>
                <a href="{{ route('vision-mission.index') }}" class="hover:text-indigo-600">Visi &amp; Misi</a>
                <span>/</span>
                <span class="text-slate-700 dark:text-slate-200">Kelola Konten</span>
            </nav>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <form action="{{ route('vision-mission.store') }}" method="POST" data-batch-form>
                @csrf
                <div class="space-y-6">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4 dark:border-slate-700 dark:bg-slate-800/40">
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <div>
                                <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">Visi Organisasi</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Masukkan judul dan deskripsi visi utama.</p>
                            </div>
                            <label class="inline-flex items-center gap-2 text-xs font-semibold text-slate-600 dark:text-slate-300">
                                <input type="hidden" name="vision_is_active" value="0">
                                <input type="checkbox" name="vision_is_active" value="1" class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" @checked(old('vision_is_active', $vision->is_active ?? true))>
                                Aktifkan visi
                            </label>
                        </div>
                        <div class="mt-4 space-y-4">
                            <label class="block">
                                <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Judul Visi</span>
                                <input type="text" name="vision_title" value="{{ old('vision_title', $vision->title ?? '') }}" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-800 dark:text-white" placeholder="Contoh: Visi FOSJABAR" required>
                                @error('vision_title')
                                    <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </label>
                            <label class="block">
                                <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Deskripsi Visi</span>
                                <textarea name="vision_content" rows="4" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-800 dark:text-white" placeholder="Tuliskan visi organisasi secara singkat." required>{{ old('vision_content', $vision->content ?? '') }}</textarea>
                                @error('vision_content')
                                    <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </label>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 p-4 shadow-sm dark:border-slate-700 dark:bg-slate-900/40">
                        <div class="mb-4 flex flex-wrap items-center justify-between gap-4">
                            <div>
                                <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">Daftar Misi</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Tambahkan poin misi yang menjelaskan langkah-langkah realisasi visi.</p>
                            </div>
                            <button type="button" class="inline-flex items-center gap-2 rounded-lg border border-indigo-200 px-3 py-1.5 text-xs font-semibold text-indigo-600 transition hover:bg-indigo-50 dark:border-indigo-500/40 dark:text-indigo-200 dark:hover:bg-indigo-500/10" data-add-mission>
                                <x-icon name="plus" class="h-3.5 w-3.5" />
                                Tambah Misi
                            </button>
                        </div>

                        <div class="space-y-3" data-mission-repeater>
                            @php
                                $oldMissions = collect(old('missions', $missions?->map(fn ($m) => ['title' => $m->title, 'content' => $m->content])->toArray() ?? ['' => ['title' => '', 'content' => '']]));
                            @endphp
                            @foreach ($oldMissions as $mission)
                                <div class="rounded-xl border border-slate-200 bg-white/70 p-4 dark:border-slate-700 dark:bg-slate-900/60" data-mission-item>
                                    <div class="mb-2 flex items-center justify-between text-xs font-semibold text-slate-500 dark:text-slate-400">
                                        <span data-mission-label>Misi #{{ $loop->iteration }}</span>
                                        <button type="button" class="text-rose-500 hover:text-rose-600 dark:text-rose-300 dark:hover:text-rose-200" data-remove-mission>Hapus</button>
                                    </div>
                                    <div class="grid gap-3 md:grid-cols-2">
                                        <label class="block">
                                            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Judul Misi (Opsional)</span>
                                            <input type="text" name="missions[{{ $loop->index }}][title]" value="{{ $mission['title'] ?? '' }}" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-800 dark:text-white" placeholder="Contoh: Pengembangan Kapasitas">
                                        </label>
                                        <label class="block">
                                            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Deskripsi Misi</span>
                                            <textarea name="missions[{{ $loop->index }}][content]" rows="3" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-800 dark:text-white" placeholder="Jelaskan poin misi" required>{{ $mission['content'] ?? '' }}</textarea>
                                            @error('missions.' . $loop->index . '.content')
                                                <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                            @enderror
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <template id="mission-template">
                            <div class="rounded-xl border border-slate-200 bg-white/70 p-4 dark:border-slate-700 dark:bg-slate-900/60" data-mission-item>
                                <div class="mb-2 flex items-center justify-between text-xs font-semibold text-slate-500 dark:text-slate-400">
                                    <span data-mission-label>Misi</span>
                                    <button type="button" class="text-rose-500 hover:text-rose-600 dark:text-rose-200 dark:hover:text-rose-100" data-remove-mission>Hapus</button>
                                </div>
                                <div class="grid gap-3 md:grid-cols-2">
                                    <label class="block">
                                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Judul Misi (Opsional)</span>
                                        <input type="text" name="missions[__INDEX__][title]" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-800 dark:text-white" placeholder="Contoh: Pengembangan Kapasitas">
                                    </label>
                                    <label class="block">
                                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Deskripsi Misi</span>
                                        <textarea name="missions[__INDEX__][content]" rows="3" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-800 dark:text-white" placeholder="Jelaskan poin misi" required></textarea>
                                    </label>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="flex flex-wrap items-center justify-end gap-3">
                        <a href="{{ request('redirect', route('vision-mission.index')) }}" class="inline-flex items-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800/70">
                            Batal
                        </a>
                        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700">
                            Simpan Konten
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector('form[data-batch-form]');
            if (!form) return;

            const repeater = form.querySelector('[data-mission-repeater]');
            const addBtn = form.querySelector('[data-add-mission]');
            const template = document.getElementById('mission-template');

            function reindex() {
                repeater.querySelectorAll('[data-mission-item]').forEach((item, index) => {
                    item.querySelector('[data-mission-label]').textContent = `Misi #${index + 1}`;
                    item.querySelectorAll('input[name^="missions["], textarea[name^="missions["]').forEach((field) => {
                        field.name = field.name.replace(/missions\[\d+]/, `missions[${index}]`);
                    });
                });
            }

            addBtn?.addEventListener('click', () => {
                if (!template || !repeater) return;
                const clone = template.innerHTML.replace(/__INDEX__/g, repeater.children.length);
                repeater.insertAdjacentHTML('beforeend', clone);
                reindex();
            });

            repeater?.addEventListener('click', (event) => {
                const removeBtn = event.target.closest('[data-remove-mission]');
                if (!removeBtn) return;
                const item = removeBtn.closest('[data-mission-item]');
                if (!item) return;
                if (repeater.children.length === 1) {
                    item.querySelectorAll('input, textarea').forEach((field) => field.value = '');
                    return;
                }
                item.remove();
                reindex();
            });

            reindex();
        });
    </script>
</x-app-layout>
