@php
    $datetimeValue = old('datetime', optional($activity->datetime)->format('Y-m-d\TH:i'));
    $targetScope = old('target_scope', $activity->target_scope ?? 'all');
    $statusValue = old('status', $activity->status ?? 'scheduled');
    $selectedPositions = old('position_ids', $activity->positions->pluck('id')->all() ?? []);
@endphp

<div class="space-y-6">
    <div>
        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-100" for="name">Judul Kegiatan</label>
        <input
            type="text"
            id="name"
            name="name"
            value="{{ old('name', $activity->name) }}"
            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
            placeholder="Contoh: Rapat Koordinasi Pengurus Pusat"
            required
        >
        @error('name')<p class="mt-2 text-sm text-rose-500">{{ $message }}</p>@enderror
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-100" for="datetime">Tanggal & Waktu</label>
            <input
                type="datetime-local"
                id="datetime"
                name="datetime"
                value="{{ $datetimeValue }}"
                class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                required
            >
            @error('datetime')<p class="mt-2 text-sm text-rose-500">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-100" for="location">Lokasi</label>
            <input
                type="text"
                id="location"
                name="location"
                value="{{ old('location', $activity->location) }}"
                class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                placeholder="Tulis lokasi kegiatan (opsional)"
            >
            @error('location')<p class="mt-2 text-sm text-rose-500">{{ $message }}</p>@enderror
        </div>
    </div>

    <div>
        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-100" for="desc">Deskripsi</label>
        <textarea
            id="desc"
            name="desc"
            rows="5"
            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
            placeholder="Tuliskan agenda dan catatan penting untuk kegiatan ini"
        >{{ old('desc', $activity->desc) }}</textarea>
        @error('desc')<p class="mt-2 text-sm text-rose-500">{{ $message }}</p>@enderror
    </div>

    <div x-data="{ scope: '{{ $targetScope }}' }" class="space-y-4">
        <div>
            <p class="text-sm font-semibold text-slate-700 dark:text-slate-100">Target Peserta</p>
            <div class="mt-2 flex flex-col gap-3 rounded-2xl border border-slate-200 p-4">
                <label class="inline-flex items-center gap-3">
                    <input type="radio" name="target_scope" value="all" @checked($targetScope === 'all') @change="scope = 'all'">
                    <span>Semua peserta terdaftar</span>
                </label>
                <label class="inline-flex items-center gap-3">
                    <input type="radio" name="target_scope" value="positions" @checked($targetScope === 'positions') @change="scope = 'positions'">
                    <span>Pilih posisi tertentu</span>
                </label>
            </div>
            @error('target_scope')<p class="mt-2 text-sm text-rose-500">{{ $message }}</p>@enderror
        </div>

        <div class="space-y-2" x-show="scope === 'positions'" x-cloak>
            <p class="text-sm font-semibold text-slate-600">Pilih Posisi</p>
            <div class="grid gap-3 rounded-2xl border border-dashed border-slate-200 p-4 md:grid-cols-2">
                @foreach($positions as $position)
                    <label class="inline-flex items-center gap-3">
                        <input
                            type="checkbox"
                            name="position_ids[]"
                            value="{{ $position->id }}"
                            @checked(in_array($position->id, (array) $selectedPositions, true))
                        >
                        <span>{{ $position->name }}</span>
                    </label>
                @endforeach
            </div>
            @error('position_ids')<p class="mt-2 text-sm text-rose-500">{{ $message }}</p>@enderror
        </div>
    </div>

    <div>
        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-100" for="status">Status</label>
        <select id="status" name="status" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
            <option value="scheduled" @selected($statusValue === 'scheduled')>Terjadwal</option>
            <option value="completed" @selected($statusValue === 'completed')>Selesai</option>
            <option value="cancelled" @selected($statusValue === 'cancelled')>Dibatalkan</option>
        </select>
        @error('status')<p class="mt-2 text-sm text-rose-500">{{ $message }}</p>@enderror
    </div>
</div>
