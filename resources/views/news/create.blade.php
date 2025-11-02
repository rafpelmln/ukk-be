<x-app-layout>
    <x-slot name="header">
        <div class="flex w-full flex-col gap-3">
            <h1 class="text-3xl font-semibold text-slate-900 dark:text-white">Buat News</h1>
            <nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                <a href="{{ route('dashboard') }}" class="hover:text-indigo-600">Dashboard</a>
                <span>/</span>
                <a href="{{ route('news.index') }}" class="hover:text-indigo-600">News</a>
                <span>/</span>
                <span class="text-slate-700 dark:text-slate-200">Buat</span>
            </nav>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <form action="{{ route('news.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="grid gap-4">
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Kategori</span>
                        <select
                            name="category_id"
                            class="mt-1 w-full rounded-md border-slate-200 px-3 py-2"
                        >
                            <option value="">Pilih kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Kategori opsional, dapat dikosongkan bila belum tersedia.</p>
                    </label>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <label class="block">
                            <span class="text-sm text-slate-700 dark:text-slate-300">Title</span>
                            <input 
                                type="text" 
                                name="title" 
                                id="title" 
                                value="{{ old('title') }}" 
                                class="mt-1 w-full rounded-md border-slate-200 px-3 py-2" 
                                required>
                        </label>
                    </div>
                    <label class="block">
                        <span class="text-sm text-slate-700 dark:text-slate-300">Subtitle</span>
                        <input type="text" name="subtitle" value="{{ old('subtitle') }}" class="mt-1 w-full rounded-md border-slate-200 px-3 py-2">
                    </label>

                    <label class="block">
                        <span class="text-sm text-slate-700 dark:text-slate-300">Author</span>
                        <input type="text" name="author" value="{{ old('author') }}" class="mt-1 w-full rounded-md border-slate-200 px-3 py-2">
                    </label>

                    <label class="block">
                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Deskripsi</span>
                        <textarea
                            id="deskripsi"
                            name="deskripsi"
                            class="mt-1 w-full rounded-md border-slate-200 px-3 py-2"
                        >{{ old('deskripsi') }}</textarea>
                    </label>

                    <label class="block">
                        <span class="text-sm text-slate-700 dark:text-slate-300">Photo (opsional)</span>
                        <input type="file" name="photo" class="mt-1">
                    </label>

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ request('redirect', route('news.index')) }}" class="inline-flex items-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800/80">
                            Batal
                        </a>
                        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700">
                            Simpan News
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
            const textarea = document.getElementById('deskripsi');
            const editor = SUNEDITOR.create(textarea, {
                height: 320,
                buttonList: [
                    ['undo', 'redo'],
                    ['font', 'fontSize', 'formatBlock'],
                    ['bold', 'italic', 'underline', 'strike', 'subscript', 'superscript'],
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
        });
    </script>
</x-app-layout>
