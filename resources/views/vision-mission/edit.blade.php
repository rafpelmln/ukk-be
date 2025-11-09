<x-app-layout>
    <x-slot name="header">
        <div class="flex w-full flex-col gap-3">
            <h1 class="text-3xl font-semibold text-slate-900 dark:text-white">Edit {{ $entry->type === 'vision' ? 'Visi' : 'Misi' }}</h1>
            <nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                <a href="{{ route('dashboard') }}" class="hover:text-indigo-600">Dashboard</a>
                <span>/</span>
                <a href="{{ route('vision-mission.index', ['type' => $entry->type]) }}" class="hover:text-indigo-600">Visi &amp; Misi</a>
                <span>/</span>
                <span class="text-slate-700 dark:text-slate-200">Edit</span>
            </nav>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <form action="{{ route('vision-mission.update', $entry) }}" method="POST" data-edit-form>
                @csrf
                @method('PUT')
                @include('vision-mission.partials.edit-form', [
                    'entry' => $entry,
                    'submitLabel' => 'Perbarui',
                ])
            </form>
        </div>
    </div>
</x-app-layout>
