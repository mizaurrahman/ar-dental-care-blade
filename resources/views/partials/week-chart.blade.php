@php
    $width = 560;
    $height = 180;
    $pad = 16;
    $totals = array_map(fn ($d) => (int) ($d['total'] ?? 0), $chart);
    $max = max(1, ...($totals ?: [0]));
    $count = count($chart);
    $step = $count > 1 ? ($width - $pad * 2) / ($count - 1) : 0;
    $pts = [];
    foreach ($chart as $i => $d) {
        $x = $pad + $i * $step;
        $y = $height - $pad - (((int) $d['total']) / $max) * ($height - $pad * 2);
        $pts[] = ['x' => $x, 'y' => $y, 'day' => $d['day']];
    }
    $points = implode(' ', array_map(fn ($p) => $p['x'].','.$p['y'], $pts));
    $lastX = $pad + max(0, $count - 1) * $step;
    $area = $pad.','.($height - $pad).' '.$points.' '.$lastX.','.($height - $pad);
@endphp
<div class="w-full">
    <svg viewBox="0 0 {{ $width }} {{ $height }}" class="h-44 w-full" role="img" aria-label="Weekly appointments">
        <defs>
            <linearGradient id="weekFill" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="#2f6fed" stop-opacity="0.28" />
                <stop offset="100%" stop-color="#2f6fed" stop-opacity="0.02" />
            </linearGradient>
        </defs>
        <polygon points="{{ $area }}" fill="url(#weekFill)" />
        <polyline points="{{ $points }}" fill="none" stroke="#2f6fed" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
        @foreach ($pts as $p)
            <circle cx="{{ $p['x'] }}" cy="{{ $p['y'] }}" r="3.5" fill="#fff" stroke="#2f6fed" stroke-width="2" />
        @endforeach
    </svg>
    <div class="mt-1 flex justify-between px-1 text-[11px] font-semibold text-[var(--muted)]">
        @foreach ($chart as $d)
            <span>{{ $d['day'] }}</span>
        @endforeach
    </div>
</div>
