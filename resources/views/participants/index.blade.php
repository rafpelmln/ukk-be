<x-app-layout>
    <x-slot name="header">
        <div class="flex w-full flex-col gap-3">
            <h1 class="text-3xl font-semibold text-slate-900 dark:text-white">Manajemen Peserta</h1>
            <nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                <a href="{{ route('dashboard') }}" class="hover:text-indigo-600 focus:text-indigo-600 dark:hover:text-indigo-300 dark:focus:text-indigo-300">Dashboard</a>
                <span>/</span>
                <span class="text-slate-700 dark:text-slate-200">Peserta</span>
            </nav>
        </div>
    </x-slot>

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-slate-900 dark:text-white">Daftar Peserta</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">Kelola data peserta, generasi, dan posisi mereka.</p>
            </div>
            <a href="{{ route('participants.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700">
                <i class="fa-solid fa-plus text-xs"></i>
                Tambah Peserta
            </a>
        </div>

        @if (session('status'))
            <div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-900/50 dark:bg-emerald-900/20 dark:text-emerald-200">
                {{ session('status') }}
            </div>
        @endif

        <div class="mt-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <form method="GET" action="{{ route('participants.index') }}" class="flex w-full flex-col gap-3 md:w-auto md:flex-row md:items-center">
                <div class="relative flex-1 md:w-80">
                    <input type="search" name="query" value="{{ $search }}" placeholder="Cari nama, username, atau email" class="w-full rounded-lg border border-slate-300 bg-slate-50 py-3 pl-11 pr-4 text-sm text-slate-700 shadow-sm transition focus:border-indigo-400 focus:bg-white focus:outline-none focus:ring focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:border-indigo-500">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                </div>
            </form>
            <div class="text-sm text-slate-500 dark:text-slate-400">
                Total peserta: <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $participants->total() }}</span>
            </div>
        </div>

        <div class="mt-6 overflow-hidden rounded-xl border border-slate-200 dark:border-slate-800">
            <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-slate-800">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:bg-slate-800 dark:text-slate-300">
                    <tr>
                        <th class="px-6 py-3">Peserta</th>
                        <th class="px-6 py-3">Kontak</th>
                        <th class="px-6 py-3">Generasi</th>
                        <th class="px-6 py-3">Posisi</th>
                        <th class="px-6 py-3">Bergabung</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white dark:divide-slate-800 dark:bg-slate-900">
                    @forelse ($participants as $participant)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="font-semibold text-slate-900 dark:text-white">{{ $participant->name }}</span>
                                    <span class="text-sm text-slate-500 dark:text-slate-400">{{ $participant->username }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-slate-600 dark:text-slate-300">{{ $participant->email }}</span>
                                    <span class="text-sm text-slate-500 dark:text-slate-400">{{ $participant->no_hp }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ optional($participant->generation)->name ?? '—' }}</td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-2">
                                    @forelse ($participant->positions as $position)
                                        <span class="inline-flex rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700 dark:bg-indigo-900/20 dark:text-indigo-200">{{ $position->name }}</span>
                                    @empty
                                        <span class="text-sm text-slate-500 dark:text-slate-400">—</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ optional($participant->created_at)->translatedFormat('d M Y') }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('participants.edit', $participant) }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:border-indigo-400 hover:text-indigo-600 dark:border-slate-700 dark:text-slate-300 dark:hover:border-indigo-400 dark:hover:text-indigo-300">
                                        <i class="fa-solid fa-pen text-xs"></i>
                                        Edit
                                    </a>
                                    <form id="delete-form-{{ $participant->id }}" action="{{ route('participants.destroy', $participant) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" data-confirm-delete="delete-form-{{ $participant->id }}" data-participant-name="{{ $participant->name }}" class="inline-flex items-center gap-2 rounded-lg border border-rose-200 px-3 py-2 text-xs font-semibold text-rose-600 transition hover:bg-rose-50 dark:border-rose-900/40 dark:text-rose-300 dark:hover:bg-rose-900/20">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-sm text-slate-500 dark:text-slate-400">
                                Belum ada peserta terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $participants->withQueryString()->links('vendor.pagination.tailwind-simple') }}
        </div>
        <div class="mt-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <form method="GET" action="{{ route('participants.index') }}" class="flex w-full flex-col gap-3 md:w-auto md:flex-row md:items-center">
                <div class="flex items-center gap-2">
                    <label for="participants-per-page" class="text-sm text-slate-500 dark:text-slate-400">Tampilkan</label>
                    <select id="participants-per-page" name="per_page" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 transition hover:border-indigo-400 focus:border-indigo-400 focus:outline-none focus:ring focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                        @foreach ([10, 25, 50, 100] as $option)
                            <option value="{{ $option }}" @selected($perPage === $option)>{{ $option }}</option>
                        @endforeach
                    </select>
                    <span class="text-sm text-slate-500 dark:text-slate-400">per halaman</span>
                </div>
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:border-indigo-400 hover:text-indigo-600 dark:border-slate-700 dark:text-slate-300 dark:hover:border-indigo-400 dark:hover:text-indigo-300">
                    Terapkan
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
