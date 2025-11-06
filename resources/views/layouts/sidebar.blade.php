@php
    $menus = [
        ['icon' => 'home', 'label' => 'Dashboard', 'route' => route('dashboard'), 'active' => ['dashboard']],
        [
            'icon' => 'newspaper',
            'label' => 'News',
            'active' => [
                'news.categories.*',
                'news.tags.*',
                'news.index',
                'news.create',
                'news.edit',
                'news.show',
                'news.store',
                'news.update',
            ],
            'children' => [
                ['label' => 'Kategori', 'route' => route('news.categories.index'), 'active' => ['news.categories.*']],
                ['label' => 'Tag', 'route' => route('news.tags.index'), 'active' => ['news.tags.*']],
                [
                    'label' => 'News',
                    'route' => route('news.index'),
                    'active' => [
                        'news.index',
                        'news.create',
                        'news.edit',
                        'news.show',
                        'news.store',
                        'news.update',
                    ],
                ],
            ],
        ],
        [
            'icon' => 'calendar-days',
            'label' => 'Events',
            'route' => route('events.index'),
            'active' => [
                'events.index',
                'events.create',
                'events.store',
                'events.edit',
                'events.update',
                'events.destroy',
            ],
        ],
        [
            'icon' => 'users',
            'label' => 'Participants',
            'active' => ['participants.*', 'position-requests.*'],
            'children' => [
                ['label' => 'Peserta', 'route' => route('participants.index'), 'active' => ['participants.*']],
                ['label' => 'Pengajuan', 'route' => route('position-requests.index'), 'active' => ['position-requests.*']],
            ],
        ],
        [
            'icon' => 'cog',
            'label' => 'Settings',
            'active' => ['generations.*', 'positions.*'],
            'children' => [
                ['label' => 'Generasi', 'route' => route('generations.index'), 'active' => ['generations.*']],
                ['label' => 'Posisi', 'route' => route('positions.index'), 'active' => ['positions.*']],
            ],
        ],
    ];

    $navBaseClasses = 'group flex w-full items-center rounded-xl py-3 text-sm font-medium transition-colors';
    $navExpandedSpacing = 'px-4 gap-3';
    $navCollapsedSpacing = 'px-3 justify-center';
    $navActiveClasses = 'bg-indigo-100 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-200';
    $navInactiveClasses = 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800';
    $childNavBaseClasses = 'flex items-center gap-3 rounded-xl px-4 py-2 text-sm transition-colors';
    $childNavActiveClasses = 'bg-indigo-100 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-200';
    $childNavInactiveClasses = 'text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800';
    $iconBaseClasses = 'h-5 w-5 flex-shrink-0 transition-colors';
    $iconActiveClasses = 'text-indigo-600 dark:text-indigo-300';
    $iconInactiveClasses = 'text-slate-400 group-hover:text-indigo-600 dark:text-slate-500 dark:group-hover:text-indigo-200';
    $currentUrl = url()->current();
@endphp

<div x-data="{}" x-cloak>
    <!-- Mobile sidebar -->
    <div
        x-show="$store.layout.mobileSidebarOpen"
        x-transition.opacity
        class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm lg:hidden"
        @click="$store.layout.closeMobileSidebar()"
    ></div>

    <aside
        x-show="$store.layout.mobileSidebarOpen"
        x-transition:enter="transform transition ease-out duration-200"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transform transition ease-in duration-150"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="fixed inset-y-0 left-0 z-50 flex w-72 flex-col border-r border-slate-200 bg-white/95 px-5 py-6 shadow-xl backdrop-blur dark:border-slate-800 dark:bg-slate-900/95 lg:hidden"
    >
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-500/10 text-indigo-600 dark:text-indigo-300">
                    <span class="text-lg font-semibold">CL</span>
                </div>
                <span class="text-lg font-semibold text-slate-900 dark:text-white">Codinglab</span>
            </div>
            <button
                type="button"
                class="rounded-full p-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800"
                @click="$store.layout.closeMobileSidebar()"
                aria-label="Close sidebar"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>

        <nav class="mt-6 flex-1 overflow-y-auto">
            <div class="space-y-1">
                @foreach ($menus as $menu)
                    @php
                        $hasChildren = isset($menu['children']) && is_array($menu['children']);
                        $childActive = false;
                        $menuActivePatterns = $menu['active'] ?? [];

                        if ($hasChildren) {
                            foreach ($menu['children'] as $child) {
                                $childActivePatterns = $child['active'] ?? [];
                                $matchesRoute = !empty($child['route']) && $child['route'] !== '#' && $currentUrl === $child['route'];
                                $matchesName = !empty($childActivePatterns) && request()->routeIs(...$childActivePatterns);

                                if ($matchesRoute || $matchesName) {
                                    $childActive = true;
                                    break;
                                }
                            }
                        }

                        $routeMatches = isset($menu['route']) && $menu['route'] !== '#' && $currentUrl === $menu['route'];
                        $patternMatches = !empty($menuActivePatterns) && request()->routeIs(...$menuActivePatterns);

                        $isActive = $hasChildren
                            ? ($childActive || $patternMatches)
                            : ($routeMatches || $patternMatches);
                    @endphp

                    @if ($hasChildren)
                        <div x-data="{ open: {{ $childActive ? 'true' : 'false' }} }" class="space-y-1">
                            <button
                                type="button"
                                class="{{ $navBaseClasses }} {{ $isActive ? $navActiveClasses : $navInactiveClasses }} px-4 gap-3 justify-between"
                                @click="open = !open"
                            >
                                <span class="flex items-center gap-3">
                                    <x-icon :name="$menu['icon']" class="{{ $iconBaseClasses }} {{ $isActive ? $iconActiveClasses : $iconInactiveClasses }}" />
                                    <span>{{ $menu['label'] }}</span>
                                </span>
                                <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                            </button>

                            <div class="space-y-1 pl-11" x-show="open" x-transition.opacity x-transition.duration.150ms>
                                @foreach ($menu['children'] as $child)
                                    @php
                                        $childActivePatterns = $child['active'] ?? [];
                                        $childIsActive = (!empty($child['route']) && $child['route'] !== '#' && $currentUrl === $child['route'])
                                            || (!empty($childActivePatterns) && request()->routeIs(...$childActivePatterns));
                                    @endphp

                                    <a
                                        href="{{ $child['route'] }}"
                                        title="{{ $child['label'] }}"
                                        class="{{ $childNavBaseClasses }} {{ $childIsActive ? $childNavActiveClasses : $childNavInactiveClasses }}"
                                        @click="$store.layout.closeMobileSidebar()"
                                        @if($childIsActive) aria-current="page" @endif
                                    >
                                        <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                        <span>{{ $child['label'] }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a
                            href="{{ $menu['route'] }}"
                            title="{{ $menu['label'] }}"
                            class="{{ $navBaseClasses }} {{ $isActive ? $navActiveClasses : $navInactiveClasses }} px-4 gap-3 justify-start"
                            @click="$store.layout.closeMobileSidebar()"
                            @if($isActive) aria-current="page" @endif
                        >
                            <x-icon :name="$menu['icon']" class="{{ $iconBaseClasses }} {{ $isActive ? $iconActiveClasses : $iconInactiveClasses }}" />
                            <span>{{ $menu['label'] }}</span>
                        </a>
                    @endif
                @endforeach
            </div>
        </nav>
    </aside>

    <!-- Desktop sidebar -->
    <aside
        class="sticky top-0 hidden h-screen flex-col overflow-y-auto border-r border-slate-200 bg-white/95 px-4 py-6 text-slate-700 backdrop-blur transition-[width] duration-300 dark:border-slate-800 dark:bg-slate-900/95 dark:text-slate-200 lg:flex"
        :class="$store.layout.sidebarExpanded ? 'w-72' : 'w-24'"
    >
        <div class="flex items-center" :class="$store.layout.sidebarExpanded ? 'justify-start' : 'justify-center'">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-indigo-500/10 text-xl font-semibold text-indigo-600 dark:text-indigo-300">
                    CL
                </div>
                <span
                    class="text-lg font-semibold text-slate-900 dark:text-white"
                    x-show="$store.layout.sidebarExpanded"
                    x-transition.opacity
                >Codinglab</span>
            </div>
        </div>

        <nav class="mt-8 flex-1 overflow-y-auto">
            <div class="space-y-1">
                @foreach ($menus as $menu)
                    @php
                        $hasChildren = isset($menu['children']) && is_array($menu['children']);
                        $childActive = false;
                        $menuActivePatterns = $menu['active'] ?? [];

                        if ($hasChildren) {
                            foreach ($menu['children'] as $child) {
                                $childActivePatterns = $child['active'] ?? [];
                                $matchesRoute = !empty($child['route']) && $child['route'] !== '#' && $currentUrl === $child['route'];
                                $matchesName = !empty($childActivePatterns) && request()->routeIs(...$childActivePatterns);

                                if ($matchesRoute || $matchesName) {
                                    $childActive = true;
                                    break;
                                }
                            }
                        }

                        $routeMatches = isset($menu['route']) && $menu['route'] !== '#' && $currentUrl === $menu['route'];
                        $patternMatches = !empty($menuActivePatterns) && request()->routeIs(...$menuActivePatterns);

                        $isActive = $hasChildren
                            ? ($childActive || $patternMatches)
                            : ($routeMatches || $patternMatches);
                    @endphp

                    @if ($hasChildren)
                        <div x-data="{ open: {{ $childActive ? 'true' : 'false' }} }" class="space-y-1">
                            <button
                                type="button"
                                class="{{ $navBaseClasses }} {{ $isActive ? $navActiveClasses : $navInactiveClasses }}"
                                :class="$store.layout.sidebarExpanded ? '{{ $navExpandedSpacing }} justify-between' : '{{ $navCollapsedSpacing }}'"
                                @click="
                                    if (!$store.layout.sidebarExpanded) {
                                        $store.layout.sidebarExpanded = true;
                                        $store.layout.persistSidebarState();
                                        open = true;
                                    } else {
                                        open = !open;
                                    }
                                "
                            >
                                <span class="flex items-center gap-3">
                                    <x-icon :name="$menu['icon']" class="{{ $iconBaseClasses }} {{ $isActive ? $iconActiveClasses : $iconInactiveClasses }}" />
                                    <span
                                        x-show="$store.layout.sidebarExpanded"
                                        x-transition.opacity
                                    >{{ $menu['label'] }}</span>
                                    <span x-show="!$store.layout.sidebarExpanded" class="sr-only">{{ $menu['label'] }}</span>
                                </span>
                                <i
                                    x-show="$store.layout.sidebarExpanded"
                                    class="fa-solid fa-chevron-down text-xs transition-transform duration-200"
                                    :class="open ? 'rotate-180' : ''"
                                ></i>
                            </button>

                            <div
                                class="space-y-1"
                                x-show="open && $store.layout.sidebarExpanded"
                                x-transition.opacity
                            >
                                @foreach ($menu['children'] as $child)
                                    @php
                                        $childActivePatterns = $child['active'] ?? [];
                                        $childIsActive = (!empty($child['route']) && $child['route'] !== '#' && $currentUrl === $child['route'])
                                            || (!empty($childActivePatterns) && request()->routeIs(...$childActivePatterns));
                                    @endphp

                                    <a
                                        href="{{ $child['route'] }}"
                                        title="{{ $child['label'] }}"
                                        class="ml-11 {{ $childNavBaseClasses }} {{ $childIsActive ? $childNavActiveClasses : $childNavInactiveClasses }}"
                                        @if($childIsActive) aria-current="page" @endif
                                    >
                                        <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                        <span>{{ $child['label'] }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a
                            href="{{ $menu['route'] }}"
                            title="{{ $menu['label'] }}"
                            class="{{ $navBaseClasses }} {{ $isActive ? $navActiveClasses : $navInactiveClasses }}"
                            :class="$store.layout.sidebarExpanded ? '{{ $navExpandedSpacing }} justify-start' : '{{ $navCollapsedSpacing }}'"
                            @if($isActive) aria-current="page" @endif
                        >
                            <x-icon :name="$menu['icon']" class="{{ $iconBaseClasses }} {{ $isActive ? $iconActiveClasses : $iconInactiveClasses }}" />
                            <span
                                x-show="$store.layout.sidebarExpanded"
                                x-transition.opacity
                            >{{ $menu['label'] }}</span>
                            <span x-show="!$store.layout.sidebarExpanded" class="sr-only">{{ $menu['label'] }}</span>
                        </a>
                    @endif
                @endforeach
            </div>
        </nav>

        <div class="mt-auto rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm dark:border-slate-800 dark:bg-slate-800/60">
            <p class="font-semibold text-slate-900 dark:text-white" x-show="$store.layout.sidebarExpanded">Sambutan 👋</p>
            <p class="mt-1 leading-relaxed text-slate-600 dark:text-slate-300" x-show="$store.layout.sidebarExpanded">
                Kelola data pengguna dan berita dengan lebih mudah lewat dashboard baru.
            </p>
            <div class="flex items-center justify-center" x-show="!$store.layout.sidebarExpanded">
                <span class="text-lg">👋</span>
            </div>
        </div>
    </aside>
</div>
