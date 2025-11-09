<x-app-layout>
    <x-slot name="header">
        <div class="flex w-full flex-col gap-3">
            <h1 class="text-3xl font-semibold text-slate-900 dark:text-white">Struktur Kepemimpinan</h1>
            <nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                <a href="{{ route('dashboard') }}" class="hover:text-indigo-600">Dashboard</a>
                <span>/</span>
                <span class="text-slate-700 dark:text-slate-200">About</span>
                <span>/</span>
                <span class="text-slate-700 dark:text-slate-200">Struktur Kepemimpinan</span>
            </nav>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="max-w-2xl text-sm text-slate-500 dark:text-slate-400">
                    Kelola ketua umum dan jabatan pendukung untuk setiap periode. Aktifkan satu periode untuk ditampilkan
                    pada halaman About, sisanya otomatis ditandai sebagai arsip.
                </div>
                <a href="{{ route('leadership-structures.create', ['redirect' => request()->fullUrl()]) }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700">
                    <x-icon name="plus" class="h-4 w-4" />
                    Tambah Periode
                </a>
            </div>

            @if(session('success'))
                <div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-400/40 dark:bg-emerald-900/40 dark:text-emerald-200">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mt-6 flex flex-wrap items-center justify-between gap-3">
                <p class="text-sm text-slate-500 dark:text-slate-400">Total {{ $structures->total() }} periode tersimpan.</p>
                <form method="GET">
                    <label class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                        Tampilkan
                        <select name="per_page" onchange="this.form.submit()" class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm dark:border-slate-700 dark:bg-slate-800">
                            @foreach ([5, 10, 25, 50] as $option)
                                <option value="{{ $option }}" @selected((int) request('per_page', $perPage) === $option)>{{ $option }}</option>
                            @endforeach
                        </select>
                        data
                    </label>
                </form>
            </div>

            <div class="mt-4">
                @if ($structures->count())
                    <div class="overflow-hidden rounded-xl border border-slate-200 dark:border-slate-800">
                        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                            <thead class="bg-slate-50 dark:bg-slate-800/60">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">Periode</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">Ketua</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">Jabatan Pendukung</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">Status</th>
                                    <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white dark:divide-slate-800 dark:bg-slate-900">
                                @foreach ($structures as $structure)
                                    <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/50">
                                        <td class="px-6 py-4 text-sm font-semibold text-slate-800 dark:text-slate-100">
                                            <div>{{ $structure->period_label }}</div>
                                            <div class="text-xs font-normal text-slate-500 dark:text-slate-400">{{ $structure->period_year }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="h-12 w-12 overflow-hidden rounded-full border border-slate-200 bg-slate-100 dark:border-slate-700 dark:bg-slate-800">
                                                    @if($structure->general_leader_photo_path)
                                                        <img src="{{ asset($structure->general_leader_photo_path) }}" alt="{{ $structure->general_leader_name }}" class="h-full w-full object-cover">
                                                    @else
                                                        <div class="flex h-full items-center justify-center text-[10px] uppercase tracking-wide text-slate-400">N/A</div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">{{ $structure->general_leader_name }}</p>
                                                    <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Ketua Umum</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">
                                            <div class="space-y-3">
                                                @forelse ($structure->roles as $role)
                                                    <div class="flex items-center gap-3">
                                                        <div class="h-10 w-10 overflow-hidden rounded-full border border-slate-200 bg-slate-100 dark:border-slate-700 dark:bg-slate-800">
                                                            @if($role->photo_path)
                                                                <img src="{{ asset($role->photo_path) }}" alt="{{ $role->person_name }}" class="h-full w-full object-cover">
                                                            @else
                                                                <div class="flex h-full items-center justify-center text-[10px] uppercase tracking-wide text-slate-400">N/A</div>
                                                            @endif
                                                        </div>
                                                        <div>
                                                            <p class="font-medium text-slate-800 dark:text-slate-200">{{ $role->person_name }}</p>
                                                            <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ $role->title }}</p>
                                                        </div>
                                                    </div>
                                                @empty
                                                    <p class="text-xs text-slate-400 dark:text-slate-500">Belum ada jabatan tambahan.</p>
                                                @endforelse
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold {{ $structure->is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400' }}">
                                                <span class="h-2 w-2 rounded-full {{ $structure->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                                {{ $structure->is_active ? 'Aktif' : 'Arsip' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-wrap items-center justify-center gap-2">
                                                <a href="{{ route('leadership-structures.edit', $structure) }}" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-600 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800/70">
                                                    Edit
                                                </a>
                                                <form action="{{ route('leadership-structures.toggle', $structure) }}" method="POST" onsubmit="return confirm('Ubah status periode ini?')">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="inline-flex items-center gap-1 rounded-lg border border-indigo-200 px-3 py-1.5 text-xs font-semibold text-indigo-600 transition hover:bg-indigo-50 dark:border-indigo-500/40 dark:text-indigo-300 dark:hover:bg-indigo-500/10">
                                                        {{ $structure->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                                    </button>
                                                </form>
                                                <form action="{{ route('leadership-structures.destroy', $structure) }}" method="POST" onsubmit="return confirm('Hapus periode ini secara permanen?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center gap-1 rounded-lg border border-rose-200 px-3 py-1.5 text-xs font-semibold text-rose-600 transition hover:bg-rose-50 dark:border-rose-500/40 dark:text-rose-300 dark:hover:bg-rose-500/10">
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
                            Menampilkan <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $structures->firstItem() }}</span>
                            &ndash; <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $structures->lastItem() }}</span>
                            dari <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $structures->total() }}</span> periode.
                        </div>
                        {{ $structures->onEachSide(1)->links('vendor.pagination.tailwind-simple') }}
                    </div>
                @else
                    <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-10 text-center text-sm text-slate-500 dark:border-slate-700 dark:bg-slate-900/60 dark:text-slate-300">
                        Belum ada data struktur kepemimpinan. Klik &ldquo;Tambah Periode&rdquo; untuk mulai mengisi.
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
