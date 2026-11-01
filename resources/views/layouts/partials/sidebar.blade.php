@props(['menu' => [], 'active' => ''])

<aside id="sidebar" class="sidebar-fixed w-64 shrink-0 text-slate-100 flex flex-col print:hidden">
    <div class="h-16 flex items-center gap-3 px-5 border-b border-white/10">
        <span class="w-9 h-9 rounded-xl bg-white/15 grid place-items-center font-bold text-white">Q</span>
        <div class="leading-tight">
            <div class="font-semibold text-white text-sm">Smart Queue</div>
            <div class="text-[11px] opacity-70">{{ auth()->user()->place?->name ?? 'All Places' }}</div>
        </div>
    </div>

    <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1 text-sm">
        @foreach ($menu as $section => $items)
            @if ($section)
                <div class="px-3 pt-4 pb-1 text-[11px] uppercase tracking-wider opacity-50">{{ $section }}</div>
            @endif

            @foreach ($items as $item)
                @php
                    $isActive = $active === $item['active'] || ($active !== '' && str_starts_with($active, $item['active'].'.'));
                @endphp
                <a href="{{ $item['url'] }}"
                   class="side-link {{ $isActive ? 'side-link-active' : '' }}">
                    <span class="w-5 text-center">{!! $item['icon'] !!}</span>
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        @endforeach
    </nav>

    <div class="p-3 border-t border-white/10">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="side-link w-full text-left">
                <span class="w-5 text-center">⎋</span>
                <span>Log out</span>
            </button>
        </form>
    </div>
</aside>
