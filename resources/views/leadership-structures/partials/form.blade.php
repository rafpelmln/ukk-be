@php
    $structure = $structure ?? null;
    $rawActive = old('is_active', $structure->is_active ?? false);
    $isActive = filter_var($rawActive, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false;

    $baseRoles = $structure?->roles ?? collect();
    $oldRoles = collect(old('roles', []));

    if ($oldRoles->isNotEmpty()) {
        $roleEntries = $oldRoles->map(function ($role) use ($baseRoles) {
            $existing = $baseRoles->firstWhere('id', $role['role_id'] ?? null);
            $role['existing_photo'] = $existing?->photo_path;
            return $role;
        });
    } else {
        $roleEntries = $baseRoles->map(function ($role) {
            return [
                'role_id' => $role->id,
                'title' => $role->title,
                'person_name' => $role->person_name,
                'existing_photo' => $role->photo_path,
            ];
        });
    }

    if ($roleEntries->isEmpty()) {
        $roleEntries->push([
            'role_id' => null,
            'title' => '',
            'person_name' => '',
            'existing_photo' => null,
        ]);
    }
@endphp

<div class="grid gap-6">
    <div class="grid gap-4 md:grid-cols-2">
        <label class="block">
            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Nama Periode</span>
            <input
                type="text"
                name="period_label"
                value="{{ old('period_label', $structure->period_label ?? '') }}"
                class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                placeholder="Contoh: Periode 2025 - Sekarang"
                required
            >
            @error('period_label')
                <p class="mt-1 text-xs text-rose-600 dark:text-rose-300">{{ $message }}</p>
            @enderror
        </label>

        <label class="block">
            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Tahun Periode</span>
            <input
                type="text"
                name="period_year"
                value="{{ old('period_year', $structure->period_year ?? '') }}"
                class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                placeholder="Misal: 2025 - 2026"
                required
            >
            @error('period_year')
                <p class="mt-1 text-xs text-rose-600 dark:text-rose-300">{{ $message }}</p>
            @enderror
        </label>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-slate-50/70 px-4 py-3 dark:border-slate-700 dark:bg-slate-800/50">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">Jadikan periode aktif?</p>
                <p class="text-xs text-slate-500 dark:text-slate-400">Periode aktif tampil di laman About sebagai struktur utama.</p>
            </div>
            <label class="inline-flex items-center gap-2">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" class="h-5 w-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" data-active-toggle @checked($isActive)>
                <span class="text-sm font-semibold text-slate-700 dark:text-slate-200">Aktif</span>
            </label>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900/40">
        <div class="mb-4">
            <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">Ketua Umum</p>
            <p class="text-xs text-slate-500 dark:text-slate-400">Selalu ditampilkan untuk setiap periode.</p>
        </div>
        <div class="grid gap-4 md:grid-cols-2">
            <label class="block">
                <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Nama Ketua Umum</span>
                <input
                    type="text"
                    name="general_leader_name"
                    value="{{ old('general_leader_name', $structure->general_leader_name ?? '') }}"
                    class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                    placeholder="Masukkan nama"
                    required
                >
                @error('general_leader_name')
                    <p class="mt-1 text-xs text-rose-600 dark:text-rose-300">{{ $message }}</p>
                @enderror
            </label>

            <label class="block">
                <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Foto Ketua Umum</span>
                <input
                    type="file"
                    name="general_leader_photo"
                    accept="image/*"
                    class="mt-1 text-sm"
                    data-photo-input
                    @if(!$structure?->general_leader_photo_path) required @endif
                >
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Ukuran maksimum 2 MB. Otomatis dikompresi ke JPG.</p>
                @error('general_leader_photo')
                    <p class="mt-1 text-xs text-rose-600 dark:text-rose-300">{{ $message }}</p>
                @enderror
                @if($structure?->general_leader_photo_path)
                    <div class="mt-2 h-24 w-24 overflow-hidden rounded-xl border border-slate-200 dark:border-slate-700">
                        <img src="{{ asset($structure->general_leader_photo_path) }}" alt="{{ $structure->general_leader_name }}" class="h-full w-full object-cover">
                    </div>
                @endif
            </label>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900/40">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">Jabatan Pendukung</p>
                <p class="text-xs text-slate-500 dark:text-slate-400">Tambah jabatan seperti Ketua 1, Ketua 2, Sekretaris, dan lainnya.</p>
            </div>
            <button type="button" class="inline-flex items-center gap-2 rounded-lg border border-indigo-200 px-3 py-1.5 text-xs font-semibold text-indigo-600 transition hover:bg-indigo-50 dark:border-indigo-500/40 dark:text-indigo-200 dark:hover:bg-indigo-500/20" data-role-add>
                <x-icon name="plus" class="h-3.5 w-3.5" />
                Tambah Jabatan
            </button>
        </div>

        <div class="space-y-4" data-role-repeater>
            @foreach ($roleEntries as $index => $role)
                <div class="rounded-xl border border-slate-200 bg-white/70 p-4 dark:border-slate-700 dark:bg-slate-900/60" data-role-item>
                    <div class="mb-3 flex items-center justify-between">
                        <p class="text-sm font-semibold text-slate-700 dark:text-slate-200" data-role-number>Jabatan #{{ $loop->iteration }}</p>
                        <button type="button" class="text-xs font-semibold text-rose-600 transition hover:text-rose-700 dark:text-rose-300 dark:hover:text-rose-200" data-role-remove>Hapus</button>
                    </div>
                    <input type="hidden" value="{{ $role['role_id'] ?? '' }}" data-role-field="role_id">
                    <div class="grid gap-4 md:grid-cols-2">
                        <label class="block">
                            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Nama Jabatan</span>
                            <input
                                type="text"
                                value="{{ $role['title'] ?? '' }}"
                                class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                placeholder="Contoh: Ketua 1, Sekretaris"
                                data-role-field="title"
                                name="roles[{{ $index }}][title]"
                            >
                            @error("roles.$index.title")
                                <p class="mt-1 text-xs text-rose-600 dark:text-rose-300">{{ $message }}</p>
                            @enderror
                        </label>
                        <label class="block">
                            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Nama Anggota</span>
                            <input
                                type="text"
                                value="{{ $role['person_name'] ?? '' }}"
                                class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                placeholder="Masukkan nama orang"
                                data-role-field="person_name"
                                name="roles[{{ $index }}][person_name]"
                            >
                            @error("roles.$index.person_name")
                                <p class="mt-1 text-xs text-rose-600 dark:text-rose-300">{{ $message }}</p>
                            @enderror
                        </label>
                    </div>
                    <label class="mt-4 block">
                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Foto Anggota</span>
                        <input
                            type="file"
                            accept="image/*"
                            class="mt-1 text-sm"
                            data-photo-input
                            data-role-field="photo"
                            name="roles[{{ $index }}][photo]"
                        >
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Ukuran maksimum 2 MB. Untuk jabatan baru foto wajib diunggah.</p>
                        @error("roles.$index.photo")
                            <p class="mt-1 text-xs text-rose-600 dark:text-rose-300">{{ $message }}</p>
                        @enderror
                        @if(!empty($role['existing_photo']))
                            <div class="mt-2 flex items-center gap-3" data-existing-photo>
                                <div class="h-20 w-20 overflow-hidden rounded-full border border-slate-200 dark:border-slate-700">
                                    <img src="{{ asset($role['existing_photo']) }}" alt="{{ $role['person_name'] ?? 'Preview' }}" class="h-full w-full object-cover">
                                </div>
                                <span class="text-xs text-slate-500 dark:text-slate-400">Biarkan kosong jika tidak ingin mengubah foto.</span>
                            </div>
                        @endif
                    </label>
                </div>
            @endforeach
        </div>

        <template id="role-row-template">
            <div class="rounded-xl border border-dashed border-slate-300 bg-white/50 p-4 dark:border-slate-700 dark:bg-slate-900/50" data-role-item>
                <div class="mb-3 flex items-center justify-between">
                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-200" data-role-number>Jabatan</p>
                    <button type="button" class="text-xs font-semibold text-rose-600 transition hover:text-rose-700 dark:text-rose-300 dark:hover:text-rose-200" data-role-remove>Hapus</button>
                </div>
                <input type="hidden" data-role-field="role_id" value="">
                <div class="grid gap-4 md:grid-cols-2">
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Nama Jabatan</span>
                        <input type="text" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-800 dark:text-white" placeholder="Contoh: Ketua 1" data-role-field="title">
                    </label>
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Nama Anggota</span>
                        <input type="text" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-800 dark:text-white" placeholder="Masukkan nama" data-role-field="person_name">
                    </label>
                </div>
                <label class="mt-4 block">
                    <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Foto Anggota</span>
                    <input type="file" accept="image/*" class="mt-1 text-sm" data-photo-input data-role-field="photo">
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Ukuran maksimum 2 MB.</p>
                </label>
            </div>
        </template>
    </div>

    <div class="flex flex-wrap items-center justify-end gap-3">
        <a href="{{ $redirectUrl ?? route('leadership-structures.index') }}" class="inline-flex items-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800/70">
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
            const form = document.querySelector('form[data-leadership-form]');
            if (!form) return;

            const sizeLimit = 2 * 1024 * 1024;
            const repeater = form.querySelector('[data-role-repeater]');
            const template = document.getElementById('role-row-template');
            const addButton = form.querySelector('[data-role-add]');

            const attachPhotoWatcher = (input) => {
                if (!input || input.dataset.photoBound) return;
                input.dataset.photoBound = 'true';
                input.addEventListener('change', () => {
                    const file = input.files?.[0];
                    if (file && file.size > sizeLimit) {
                        alert('Ukuran foto melebihi 2 MB. Silakan pilih file lain.');
                        input.value = '';
                    }
                });
            };

            const refreshPhotoInputs = () => {
                form.querySelectorAll('[data-photo-input]').forEach(attachPhotoWatcher);
            };

            function reindexRoles() {
                repeater?.querySelectorAll('[data-role-item]').forEach((item, index) => {
                    item.querySelectorAll('[data-role-field]').forEach((field) => {
                        const key = field.getAttribute('data-role-field');
                        if (!key) return;
                        field.setAttribute('name', `roles[${index}][${key}]`);
                    });
                    const counter = item.querySelector('[data-role-number]');
                    if (counter) {
                        counter.textContent = `Jabatan #${index + 1}`;
                    }
                });
            }

            addButton?.addEventListener('click', () => {
                if (!template || !repeater) return;
                const fragment = template.content.cloneNode(true);
                repeater.appendChild(fragment);
                reindexRoles();
                refreshPhotoInputs();
            });

            repeater?.addEventListener('click', (event) => {
                const removeBtn = event.target.closest('[data-role-remove]');
                if (!removeBtn) return;
                const item = removeBtn.closest('[data-role-item]');
                if (!item) return;
                item.remove();
                reindexRoles();
            });

            form.addEventListener('submit', (event) => {
                for (const input of form.querySelectorAll('[data-photo-input]')) {
                    const file = input.files?.[0];
                    if (file && file.size > sizeLimit) {
                        event.preventDefault();
                        alert('Ukuran foto melebihi 2 MB. Periksa kembali file yang diunggah.');
                        return;
                    }
                }
            });

            refreshPhotoInputs();
            reindexRoles();
        });
    </script>
@endpush
