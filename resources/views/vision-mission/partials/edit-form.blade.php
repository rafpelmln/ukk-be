@php
    $mode = old('type', $entry->type ?? 'vision');
    $missionItems = $mode === 'mission'
        ? (old('content_items', preg_split("/\r\n|\n|\r/", $entry->content ?? '') ?: ['']))
        : [];
@endphp

<div class="grid gap-6">
    <div class="grid gap-4 md:grid-cols-2">
        <label class="block">
            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Jenis Konten</span>
            <select name="type" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-800 dark:text-white" data-edit-type required>
                <option value="vision" @selected($mode === 'vision')>Visi</option>
                <option value="mission" @selected($mode === 'mission')>Misi</option>
            </select>
            @error('type')
                <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
            @enderror
        </label>

        <label class="block">
            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Status</span>
            <div class="mt-1 flex items-center gap-3">
                <input type="hidden" name="is_active" value="0">
                <label class="inline-flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" class="h-5 w-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" @checked(old('is_active', $entry->is_active ?? true))>
                    <span class="text-sm text-slate-600 dark:text-slate-300">Aktif</span>
                </label>
            </div>
        </label>
    </div>

    <label class="block">
        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Judul</span>
        <input
            type="text"
            name="title"
            value="{{ old('title', $entry->title ?? '') }}"
            class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-800 dark:text-white"
            placeholder="Masukkan judul"
            required
        >
        @error('title')
            <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
        @enderror
    </label>

    <div data-edit-vision @class(['space-y-2', $mode === 'mission' ? 'hidden' : ''])>
        <label class="block">
            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Deskripsi Visi</span>
            <textarea
                name="content"
                rows="6"
                class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                placeholder="Tulis visi di sini."
            >{{ old('content', $mode === 'vision' ? ($entry->content ?? '') : '') }}</textarea>
            @error('content')
                <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
            @enderror
        </label>
    </div>

    <div data-edit-mission @class(['space-y-3', $mode === 'mission' ? '' : 'hidden'])>
        <input type="hidden" name="content" data-mission-hidden value="{{ old('content', $mode === 'mission' ? ($entry->content ?? '') : '') }}">
        <div class="flex items-center justify-between">
            <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">Poin Misi</p>
            <button type="button" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700 dark:text-indigo-300" data-edit-add-point>Tambah Poin</button>
        </div>
        <div class="space-y-2" data-edit-point-list>
            @foreach ($missionItems as $point)
                <div class="flex items-center gap-2" data-edit-point-item>
                    <input type="text" class="flex-1 rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-800 dark:text-white" name="content_items[]" value="{{ $point }}" placeholder="Tuliskan poin misi" required>
                    <button type="button" class="text-xs font-semibold text-rose-500 hover:text-rose-600 dark:text-rose-300 dark:hover:text-rose-200" data-edit-remove-point>✕</button>
                </div>
            @endforeach
        </div>
        @error('content')
            <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex flex-wrap items-center justify-end gap-3">
        <a href="{{ request('redirect', route('vision-mission.index', ['type' => $entry->type ?? 'vision'])) }}" class="inline-flex items-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800/70">
            Batal
        </a>
        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700">
            {{ $submitLabel ?? 'Simpan' }}
        </button>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('form[data-edit-form]').forEach((form) => {
                const typeSelect = form.querySelector('[data-edit-type]');
                const visionSet = form.querySelector('[data-edit-vision]');
                const missionSet = form.querySelector('[data-edit-mission]');
                const pointList = form.querySelector('[data-edit-point-list]');
                const addPointBtn = form.querySelector('[data-edit-add-point]');
                const hiddenContent = form.querySelector('[data-mission-hidden]');

                function syncVisibility() {
                    const isMission = typeSelect.value === 'mission';
                    visionSet.classList.toggle('hidden', isMission);
                    missionSet.classList.toggle('hidden', !isMission);
                }

                function addPoint(value = '') {
                    if (!pointList) {
                        return;
                    }

                    const wrapper = document.createElement('div');
                    wrapper.className = 'flex items-center gap-2';
                    wrapper.setAttribute('data-edit-point-item', '');
                    wrapper.innerHTML = `
                        <input type="text" class="flex-1 rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-800 dark:text-white" name="content_items[]" placeholder="Tuliskan poin misi" required value="${value}">
                        <button type="button" class="text-xs font-semibold text-rose-500 hover:text-rose-600 dark:text-rose-300 dark:hover:text-rose-200" data-edit-remove-point>✕</button>
                    `;
                    pointList.appendChild(wrapper);
                }

                addPointBtn?.addEventListener('click', () => addPoint());

                pointList?.addEventListener('click', (event) => {
                    const removeBtn = event.target.closest('[data-edit-remove-point]');
                    if (!removeBtn) return;
                    const item = removeBtn.closest('[data-edit-point-item]');
                    if (!item) return;
                    if (pointList.children.length === 1) {
                        item.querySelector('input').value = '';
                        return;
                    }
                    item.remove();
                });

                form.addEventListener('submit', () => {
                    if (typeSelect.value !== 'mission' || !hiddenContent) {
                        return;
                    }
                    const values = Array.from(pointList.querySelectorAll('input[name="content_items[]"]'))
                        .map((input) => input.value.trim())
                        .filter(Boolean);
                    hiddenContent.value = values.join('\n');
                });

                typeSelect?.addEventListener('change', syncVisibility);
                if (pointList && pointList.children.length === 0) {
                    addPoint();
                }
                syncVisibility();
            });
        });
    </script>
@endpush
