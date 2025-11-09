<x-app-layout>
    <x-slot name="header">
        <div class="flex w-full flex-col gap-3">
            <h1 class="text-3xl font-semibold text-slate-900 dark:text-white">Visi &amp; Misi</h1>
            <nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                <a href="{{ route('dashboard') }}" class="hover:text-indigo-600">Dashboard</a>
                <span>/</span>
                <span class="text-slate-700 dark:text-slate-200">About</span>
                <span>/</span>
                <span class="text-slate-700 dark:text-slate-200">Visi &amp; Misi</span>
            </nav>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="text-sm text-slate-500 dark:text-slate-400">
                    Kelola konten visi dan misi secara dinamis. Konten aktif akan muncul di halaman About frontend.
                </div>
                <a href="{{ route('vision-mission.create', ['redirect' => request()->fullUrl()]) }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700">
                    <x-icon name="plus" class="h-4 w-4" />
                    Kelola Visi &amp; Misi
                </a>
            </div>

            <div class="mt-6 flex flex-wrap items-center gap-3">
                <div class="inline-flex overflow-hidden rounded-full border border-slate-200 bg-slate-100 p-1 dark:border-slate-700 dark:bg-slate-800">
                    @foreach (['vision' => 'Visi', 'mission' => 'Misi'] as $option => $label)
                        <a
                            href="{{ route('vision-mission.index', ['type' => $option]) }}"
                            class="rounded-full px-4 py-1 text-sm font-semibold {{ $type === $option ? 'bg-white text-indigo-600 shadow dark:bg-slate-900/60 dark:text-indigo-300' : 'text-slate-500 dark:text-slate-300' }}"
                        >
                            {{ $label }} ({{ $counts[$option] ?? 0 }})
                        </a>
                    @endforeach
                </div>
                <form method="GET" class="ml-auto flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                    <input type="hidden" name="type" value="{{ $type }}">
                    <span>Tampilkan</span>
                    <select name="per_page" onchange="this.form.submit()" class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm dark:border-slate-700 dark:bg-slate-800">
                        @foreach ([5, 10, 25, 50] as $option)
                            <option value="{{ $option }}" @selected((int) request('per_page', $perPage) === $option)>{{ $option }}</option>
                        @endforeach
                    </select>
                    <span>data</span>
                </form>
            </div>

            @if(session('success'))
                <div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-500/40 dark:bg-emerald-900/40 dark:text-emerald-200">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mt-6">
                @if($entries->count())
                    <div class="overflow-hidden rounded-xl border border-slate-200 dark:border-slate-800">
                        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                            <thead class="bg-slate-50 dark:bg-slate-800/60">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">Judul</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">Konten</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">Status</th>
                                    <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white dark:divide-slate-800 dark:bg-slate-900">
                                @foreach ($entries as $entry)
                                    <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/50">
                                        <td class="px-6 py-4 text-sm font-semibold text-slate-800 dark:text-slate-100">
                                            <div>{{ $entry->title }}</div>
                                            <div class="text-xs text-slate-500 dark:text-slate-400">{{ ucfirst($entry->type) }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">
                                            <div class="line-clamp-3 whitespace-pre-line">{{ $entry->content }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold {{ $entry->is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400' }}">
                                                <span class="h-2 w-2 rounded-full {{ $entry->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                                {{ $entry->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-wrap items-center justify-center gap-2">
                                                <a href="{{ route('vision-mission.edit', $entry) }}" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-600 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800/70">
                                                    Edit
                                                </a>
                                                <form action="{{ route('vision-mission.toggle', $entry) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="inline-flex items-center gap-1 rounded-lg border border-indigo-200 px-3 py-1.5 text-xs font-semibold text-indigo-600 transition hover:bg-indigo-50 dark:border-indigo-500/30 dark:text-indigo-200 dark:hover:bg-indigo-500/10">
                                                        {{ $entry->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                                    </button>
                                                </form>
                                                <form action="{{ route('vision-mission.destroy', $entry) }}" method="POST" onsubmit="return confirm('Hapus entri ini permanen?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center gap-1 rounded-lg border border-rose-200 px-3 py-1.5 text-xs font-semibold text-rose-600 transition hover:bg-rose-50 dark:border-rose-500/30 dark:text-rose-200 dark:hover:bg-rose-500/10">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6 flex flex-wrap items-center justify-between gap-4">
                        <div class="text-sm text-slate-500 dark:text-slate-400">
                            Menampilkan <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $entries->firstItem() }}</span>
                            &ndash;
                            <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $entries->lastItem() }}</span>
                            dari <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $entries->total() }}</span> entri.
                        </div>
                        {{ $entries->onEachSide(1)->links('vendor.pagination.tailwind-simple') }}
                    </div>
                @else
                    <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-10 text-center text-sm text-slate-500 dark:border-slate-700 dark:bg-slate-900/60 dark:text-slate-300">
                        Belum ada data {{ $type === 'vision' ? 'visi' : 'misi' }}. Klik tombol tambah untuk membuat konten pertama.
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
