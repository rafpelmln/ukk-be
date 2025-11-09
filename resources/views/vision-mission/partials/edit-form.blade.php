<div class="grid gap-6">
    <div class="grid gap-4 md:grid-cols-2">
        <label class="block">
            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Jenis Konten</span>
            <select name="type" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-800 dark:text-white" required>
                <option value="vision" @selected(old('type', $entry->type ?? 'vision') === 'vision')>Visi</option>
                <option value="mission" @selected(old('type', $entry->type ?? 'vision') === 'mission')>Misi</option>
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

    <label class="block">
        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Konten</span>
        <textarea
            name="content"
            rows="6"
            class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-800 dark:text-white"
            placeholder="Tulis konten di sini."
            required
        >{{ old('content', $entry->content ?? '') }}</textarea>
        @error('content')
            <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
        @enderror
    </label>

    <div class="flex flex-wrap items-center justify-end gap-3">
        <a href="{{ request('redirect', route('vision-mission.index', ['type' => $entry->type ?? 'vision'])) }}" class="inline-flex items-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800/70">
            Batal
        </a>
        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700">
            {{ $submitLabel ?? 'Simpan' }}
        </button>
    </div>
</div>
