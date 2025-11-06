<x-app-layout>
    <x-slot name="header">
        <div class="flex w-full flex-col gap-3">
            <h1 class="text-3xl font-semibold text-slate-900 dark:text-white">Banner Beranda</h1>
            <nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                <a href="{{ route('dashboard') }}" class="hover:text-indigo-600">Dashboard</a>
                <span>/</span>
                <span class="text-slate-700 dark:text-slate-200">Banner Beranda</span>
            </nav>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="text-sm text-slate-500 dark:text-slate-400">
                    Gunakan menu ini untuk mengunggah banner slider di halaman home. Setiap banner otomatis aktif,
                    dan gambar akan dikompres ke JPG maksimal 2 MB.
                </div>
                <a href="{{ route('home-banners.create', ['redirect' => request()->fullUrl()]) }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700">
                    <x-icon name="plus" class="h-4 w-4" />
                    Tambah Banner
                </a>
            </div>

            @if(session('success'))
                <div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-400/40 dark:bg-emerald-900/40 dark:text-emerald-200">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mt-6">
                @if ($banners->count())
                    <div class="overflow-hidden rounded-xl border border-slate-200 dark:border-slate-700">
                        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                            <thead class="bg-slate-50 dark:bg-slate-800/60">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                        Banner
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                        Urutan
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                        Status
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white dark:divide-slate-700 dark:bg-slate-900">
                                @foreach ($banners as $banner)
                                    <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/60">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="h-16 w-28 overflow-hidden rounded-lg border border-slate-200 bg-slate-100 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                                                    @if ($banner->image_path)
                                                        <img src="{{ asset($banner->image_path) }}" alt="Home banner #{{ $banner->id }}" class="h-full w-full object-cover">
                                                    @else
                                                        <div class="flex h-full items-center justify-center text-xs text-slate-400">Tidak ada gambar</div>
                                                    @endif
                                                </div>
                                                <div class="text-xs text-slate-500 dark:text-slate-400">
                                                    <p>ID: <span class="font-semibold text-slate-700 dark:text-slate-200">#{{ $banner->id }}</span></p>
                                                    <p>Dibuat: {{ $banner->created_at?->format('d M Y H:i') ?? '—' }}</p>
                                                    <p>Diubah: {{ $banner->updated_at?->format('d M Y H:i') ?? '—' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">
                                            {{ $banner->display_order }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-medium {{ $banner->is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300' : 'bg-slate-200 text-slate-600 dark:bg-slate-700 dark:text-slate-300' }}">
                                                <span class="h-2 w-2 rounded-full {{ $banner->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                                {{ $banner->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center justify-center gap-3 text-base">
                                                <form action="{{ route('home-banners.toggle', $banner) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="text-slate-500 transition hover:text-indigo-600" title="Ubah status">
                                                        <i class="fa-solid fa-toggle-{{ $banner->is_active ? 'on text-emerald-500' : 'off' }}"></i>
                                                    </button>
                                                </form>
                                                <a href="{{ route('home-banners.edit', $banner) }}" class="text-slate-500 transition hover:text-indigo-600" title="Ganti gambar">
                                                    <i class="fa-solid fa-pen"></i>
                                                </a>
                                                <form action="{{ route('home-banners.destroy', $banner) }}" method="POST" onsubmit="return confirm('Hapus banner ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-slate-500 transition hover:text-rose-600" title="Hapus">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex flex-wrap items-center gap-3 text-sm text-slate-500 dark:text-slate-400">
                            <form method="GET" action="{{ route('home-banners.index') }}" class="flex items-center gap-2">
                                <span>Tampilkan</span>
                                <select
                                    name="per_page"
                                    class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 transition hover:border-indigo-400 focus:border-indigo-400 focus:outline-none focus:ring focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:border-indigo-400 dark:focus:border-indigo-400"
                                    onchange="this.form.submit()"
                                >
                                    @foreach ([5, 10, 25, 50, 100] as $option)
                                        <option value="{{ $option }}" @selected($perPage === $option)>{{ $option }}</option>
                                    @endforeach
                                </select>
                                <span>per halaman</span>
                                <input type="hidden" name="direction" value="{{ $direction }}">
                            </form>
                            <span>
                                Menampilkan
                                <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $banners->firstItem() }}</span>
                                sampai
                                <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $banners->lastItem() }}</span>
                                dari
                                <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $banners->total() }}</span>
                                banner
                            </span>
                        </div>
                        <div>
                            {{ $banners->onEachSide(1)->links('vendor.pagination.tailwind-simple') }}
                        </div>
                    </div>
                @else
                    <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-10 text-center text-sm text-slate-500 dark:border-slate-700 dark:bg-slate-900/50 dark:text-slate-300">
                        Belum ada banner yang ditambahkan. Klik tombol "Tambah Banner" untuk mengunggah gambar pertama.
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
