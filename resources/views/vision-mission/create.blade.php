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
                        <div class="mb-4">
                            <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">Misi</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Masukkan judul (opsional) dan daftar poin misi.</p>
                        </div>

                        @php
                            $defaultMission = $missions?->first();
                            $missionTitle = old('mission_title', $defaultMission->title ?? '');
                            $missionItems = old('mission_items', preg_split("/\r\n|\n|\r/", $defaultMission->content ?? '') ?: ['']);
                        @endphp

                        <label class="block">
                            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Judul Misi (Opsional)</span>
                            <input type="text" name="mission_title" value="{{ $missionTitle }}" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-800 dark:text-white" placeholder="Contoh: Pengembangan Kapasitas">
                        </label>

                        <div class="mt-4 rounded-xl border border-slate-200 p-3 dark:border-slate-700">
                            <div class="mb-2 flex items-center justify-between">
                                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Poin Misi</span>
                                <button type="button" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700 dark:text-indigo-300" data-add-point>Tambah Poin</button>
                            </div>
                            <div class="space-y-2" data-point-list>
                                @foreach ($missionItems as $point)
                                    <div class="flex items-center gap-2" data-point-item>
                                        <input type="text" name="mission_items[]" value="{{ $point }}" class="flex-1 rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-800 dark:text-white" placeholder="Deskripsi singkat" required>
                                        <button type="button" class="text-xs font-semibold text-rose-500 hover:text-rose-600 dark:text-rose-300 dark:hover:text-rose-200" data-remove-point>✕</button>
                                    </div>
                                @endforeach
                            </div>
                            @error('mission_items')
                                <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <template id="mission-point-template">
                            <div class="flex items-center gap-2" data-point-item>
                                <input type="text" name="mission_items[]" class="flex-1 rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-800 dark:text-white" placeholder="Deskripsi singkat" required>
                                <button type="button" class="text-xs font-semibold text-rose-500 hover:text-rose-600 dark:text-rose-300 dark:hover:text-rose-200" data-remove-point>✕</button>
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
            const pointList = document.querySelector('[data-point-list]');
            const pointTemplate = document.getElementById('mission-point-template');
            const addPointBtn = document.querySelector('[data-add-point]');

            addPointBtn?.addEventListener('click', () => {
                if (!pointTemplate || !pointList) return;
                pointList.insertAdjacentHTML('beforeend', pointTemplate.innerHTML);
            });

            pointList?.addEventListener('click', (event) => {
                const removeBtn = event.target.closest('[data-remove-point]');
                if (!removeBtn) return;
                const item = removeBtn.closest('[data-point-item]');
                if (!item) return;
                if (pointList.children.length === 1) {
                    item.querySelector('input').value = '';
                    return;
                }
                item.remove();
            });
        });
    </script>
</x-app-layout>
