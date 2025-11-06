@csrf
@if(isset($method))
    @method($method)
@endif

<div class="space-y-5">
    <label class="block">
        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Gambar Banner</span>
        <input
            type="file"
            name="image"
            id="banner-image-input"
            accept="image/*"
            class="mt-1 text-sm text-slate-600 dark:text-slate-300"
            {{ isset($banner) ? '' : 'required' }}
        >
        <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Unggah gambar maksimal 2 MB. File otomatis dikompres ke JPG.</p>
        <div id="banner-image-alert" class="mt-2 hidden rounded-lg border border-amber-300 bg-amber-50 px-3 py-2 text-xs text-amber-700 dark:border-amber-400/40 dark:bg-amber-900/30 dark:text-amber-200"></div>
        @error('image')
            <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
        @enderror
    </label>

    @if(isset($banner) && $banner->image_path)
        <div class="flex items-center gap-4 rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-800/40">
            <div class="h-24 w-40 overflow-hidden rounded-lg border border-slate-200 shadow-sm dark:border-slate-700">
                <img src="{{ asset($banner->image_path) }}" alt="Banner saat ini" class="h-full w-full object-cover">
            </div>
            <div class="text-xs text-slate-500 dark:text-slate-400">
                <p class="font-medium text-slate-600 dark:text-slate-200">Gambar saat ini</p>
                <p>Unggah file baru jika ingin mengganti.</p>
            </div>
        </div>
    @endif

    <div class="flex items-center justify-end gap-3">
        <a href="{{ request('redirect', route('home-banners.index')) }}" class="inline-flex items-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800/80">
            Batal
        </a>
        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700">
            {{ $submitLabel ?? 'Simpan' }}
        </button>
    </div>
</div>
