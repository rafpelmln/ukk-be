<x-app-layout>
    <x-slot name="header">
        <div class="flex w-full flex-col gap-3">
            <h1 class="text-3xl font-semibold text-slate-900 dark:text-white">Edit Event</h1>
            <nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                <a href="{{ route('dashboard') }}" class="hover:text-indigo-600">Dashboard</a>
                <span>/</span>
                <a href="{{ route('events.index') }}" class="hover:text-indigo-600">Events</a>
                <span>/</span>
                <span class="text-slate-700 dark:text-slate-200">Edit</span>
            </nav>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <form action="{{ route('events.update', $event) }}" method="POST" enctype="multipart/form-data" data-event-form="edit">
                @csrf
                @method('PUT')
                <div class="grid gap-4">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <label class="block">
                            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Judul</span>
                            <input
                                type="text"
                                name="title"
                                id="title"
                                value="{{ old('title', $event->title) }}"
                                class="mt-1 w-full rounded-md border-slate-200 px-3 py-2"
                                required
                            >
                        </label>

                        <label class="block">
                            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Subjudul</span>
                            <input
                                type="text"
                                name="subtitle"
                                value="{{ old('subtitle', $event->subtitle) }}"
                                class="mt-1 w-full rounded-md border-slate-200 px-3 py-2"
                            >
                        </label>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <label class="block">
                            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Tanggal Event</span>
                            <input
                                type="date"
                                name="event_date"
                                value="{{ old('event_date', optional($event->event_date)->format('Y-m-d')) }}"
                                class="mt-1 w-full rounded-md border-slate-200 px-3 py-2"
                            >
                        </label>

                        <label class="block">
                            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Lokasi</span>
                            <input
                                type="text"
                                name="location"
                                value="{{ old('location', $event->location) }}"
                                class="mt-1 w-full rounded-md border-slate-200 px-3 py-2"
                            >
                        </label>
                    </div>

                    <label class="block">
                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Deskripsi</span>
                        <textarea
                            id="description"
                            name="description"
                            class="mt-1 w-full rounded-md border-slate-200 px-3 py-2"
                        >{{ old('description', $event->description) }}</textarea>
                    </label>

                    <div class="space-y-3">
                        <label class="block">
                            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Foto Event</span>
                            <input
                                type="file"
                                name="photo"
                                id="photo-input"
                                accept="image/*"
                                class="mt-1"
                            >
                            <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Ukuran maksimum 2 MB, akan dikompresi otomatis ke sekitar 250 KB.</p>
                            <div id="photo-alert" class="mt-2 hidden rounded-lg border border-amber-300 bg-amber-50 px-3 py-2 text-xs text-amber-700"></div>
                            @error('photo')
                                <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                        </label>

                        @if ($event->photo_url)
                            <div class="flex items-center gap-3 rounded-xl border border-slate-200 p-3 dark:border-slate-700">
                                <img src="{{ $event->photo_url }}" alt="{{ $event->title }}" class="h-16 w-24 rounded-lg object-cover">
                                <div class="text-xs text-slate-500 dark:text-slate-400">
                                    Foto saat ini. Mengunggah foto baru akan menggantikan foto lama.
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ request('redirect', route('events.index')) }}" class="inline-flex items-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800/80">
                            Batal
                        </a>
                        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700">
                            Perbarui Event
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/suneditor@2.46.1/dist/css/suneditor.min.css">
    <script src="https://cdn.jsdelivr.net/npm/suneditor@2.46.1/dist/suneditor.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const textarea = document.getElementById('description');
            const editor = SUNEDITOR.create(textarea, {
                height: 320,
                buttonList: [
                    ['undo', 'redo'],
                    ['font', 'fontSize', 'formatBlock'],
                    ['bold', 'italic', 'underline', 'strike'],
                    ['fontColor', 'hiliteColor'],
                    ['align', 'list', 'table'],
                    ['link', 'image', 'video'],
                    ['blockquote', 'codeView', 'fullScreen']
                ],
                imageFileInput: false,
                defaultStyle: 'font-size:14px;'
            });

            editor.onChange = function (contents) {
                textarea.value = contents;
            };

            editor.setContents(textarea.value || '');

            const form = document.querySelector('form[data-event-form="edit"]');
            const photoInput = document.getElementById('photo-input');
            const alertBox = document.getElementById('photo-alert');
            const sizeLimit = 2 * 1024 * 1024; // 2 MB

            function showPhotoError(message) {
                if (!alertBox) return;
                alertBox.textContent = message;
                alertBox.classList.remove('hidden');
            }

            function clearPhotoError() {
                if (!alertBox) return;
                alertBox.textContent = '';
                alertBox.classList.add('hidden');
            }

            if (photoInput) {
                photoInput.addEventListener('change', function () {
                    clearPhotoError();

                    if (this.files && this.files[0] && this.files[0].size > sizeLimit) {
                        showPhotoError('Ukuran file melebihi 2 MB. Silakan pilih gambar lain.');
                        this.value = '';
                    }
                });
            }

            if (form) {
                form.addEventListener('submit', function (event) {
                    if (photoInput && photoInput.files && photoInput.files[0] && photoInput.files[0].size > sizeLimit) {
                        event.preventDefault();
                        showPhotoError('Ukuran file melebihi 2 MB. Silakan pilih gambar lain.');
                        photoInput.value = '';
                    }
                });
            }
        });
    </script>
</x-app-layout>
