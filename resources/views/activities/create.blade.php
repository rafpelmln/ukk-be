<x-app-layout>
    <x-slot name="header">
        <div class="flex w-full flex-col gap-3">
            <h1 class="text-3xl font-semibold text-slate-900 dark:text-white">Tambah Kegiatan</h1>
            <nav class="flex flex-wrap items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                <a href="{{ route('dashboard') }}" class="hover:text-indigo-600">Dashboard</a>
                <span>/</span>
                <a href="{{ route('activities.index') }}" class="hover:text-indigo-600">Kegiatan</a>
                <span>/</span>
                <span class="text-slate-700 dark:text-slate-200">Tambah</span>
            </nav>
        </div>
    </x-slot>

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900">
        <form action="{{ route('activities.store') }}" method="POST" class="space-y-6">
            @csrf
            @include('activities._form')
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('activities.index') }}" class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">Batal</a>
                <button type="submit" class="rounded-xl bg-indigo-600 px-5 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700">Simpan</button>
            </div>
        </form>
    </div>
</x-app-layout>
