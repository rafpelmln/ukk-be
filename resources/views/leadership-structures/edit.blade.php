<x-app-layout>
    <x-slot name="header">
        <div class="flex w-full flex-col gap-3">
            <h1 class="text-3xl font-semibold text-slate-900 dark:text-white">Ubah Struktur Kepemimpinan</h1>
            <nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                <a href="{{ route('dashboard') }}" class="hover:text-indigo-600">Dashboard</a>
                <span>/</span>
                <a href="{{ route('leadership-structures.index') }}" class="hover:text-indigo-600">Struktur Kepemimpinan</a>
                <span>/</span>
                <span class="text-slate-700 dark:text-slate-200">Edit</span>
            </nav>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <form action="{{ route('leadership-structures.update', $structure) }}" method="POST" enctype="multipart/form-data" data-leadership-form>
                @csrf
                @method('PUT')
                @include('leadership-structures.partials.form', [
                    'structure' => $structure,
                    'generations' => $generations,
                    'submitLabel' => 'Perbarui Periode',
                    'redirectUrl' => request('redirect', route('leadership-structures.index')),
                ])
            </form>
        </div>
    </div>
</x-app-layout>
