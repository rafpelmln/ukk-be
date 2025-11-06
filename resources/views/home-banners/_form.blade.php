@csrf
@if(isset($method))
    @method($method)
@endif

<div class="grid gap-5">
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <label class="block">
            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Judul</span>
            <input
                type="text"
                name="title"
                value="{{ old('title', $banner->title ?? '') }}"
                class="mt-1 w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:border-indigo-400 focus:ring focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:border-indigo-500"
                required
            >
        </label>

        <label class="block">
            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Subjudul</span>
            <input
                type="text"
                name="subtitle"
                value="{{ old('subtitle', $banner->subtitle ?? '') }}"
                class="mt-1 w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:border-indigo-400 focus:ring focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:border-indigo-500"
            >
        </label>
    </div>

    <label class="block">
        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Deskripsi</span>
        <textarea
            name="description"
            rows="4"
            class="mt-1 w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:border-indigo-400 focus:ring focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:border-indigo-500"
        >{{ old('description', $banner->description ?? '') }}</textarea>
        <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Deskripsi akan ditampilkan sebagai teks pendukung di slider.</p>
    </label>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <label class="block">
            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Label Tombol</span>
            <input
                type="text"
                name="button_label"
                value="{{ old('button_label', $banner->button_label ?? '') }}"
                class="mt-1 w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:border-indigo-400 focus:ring focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:border-indigo-500"
            >
        </label>

        <label class="block">
            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">URL Tombol</span>
            <input
                type="url"
                name="button_url"
                value="{{ old('button_url', $banner->button_url ?? '') }}"
                class="mt-1 w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:border-indigo-400 focus:ring focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:border-indigo-500"
                placeholder="https://"
            >
        </label>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <label class="block">
            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Urutan Tampil</span>
            <input
                type="number"
                min="0"
                name="display_order"
                value="{{ old('display_order', $banner->display_order ?? 0) }}"
                class="mt-1 w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:border-indigo-400 focus:ring focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:border-indigo-500"
            >
            <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Angka kecil tampil lebih dahulu.</p>
        </label>

        <label class="block sm:col-span-2">
            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Status Tampil</span>
            <div class="mt-3 flex items-center gap-3">
                <label class="inline-flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                    <input
                        type="hidden"
                        name="is_active"
                        value="0"
                    >
                    <input
                        type="checkbox"
                        name="is_active"
                        value="1"
                        class="h-5 w-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 dark:border-slate-600 dark:bg-slate-800"
                        @checked(old('is_active', $banner->is_active ?? true))
                    >
                    Tampilkan di home
                </label>
            </div>
        </label>
    </div>

    <label class="block">
        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Gambar Banner</span>
        <input
            type="file"
            name="image"
            id="banner-image-input"
            accept="image/*"
            class="mt-1 text-sm text-slate-600 dark:text-slate-300"
            {{ isset($banner) && $banner->image_path ? '' : 'required' }}
        >
        <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Ukuran maksimum 2 MB. Gambar akan otomatis dikompres ke format JPG.</p>
        <div id="banner-image-alert" class="mt-2 hidden rounded-lg border border-amber-300 bg-amber-50 px-3 py-2 text-xs text-amber-700 dark:border-amber-400/40 dark:bg-amber-900/30 dark:text-amber-200"></div>
        @if(isset($banner) && $banner->image_path)
            <div class="mt-4 flex items-center gap-4">
                <div class="h-24 w-40 overflow-hidden rounded-lg border border-slate-200 shadow-sm dark:border-slate-700">
                    <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" class="h-full w-full object-cover">
                </div>
                <div class="text-xs text-slate-500 dark:text-slate-400">
                    <p>Gambar saat ini.</p>
                    <p>Unggah file baru untuk mengganti.</p>
                </div>
            </div>
        @endif
        @error('image')
            <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
        @enderror
    </label>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ request('redirect', route('home-banners.index')) }}" class="inline-flex items-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800/80">
            Batal
        </a>
        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700">
            {{ $submitLabel ?? 'Simpan Banner' }}
        </button>
    </div>
</div>
