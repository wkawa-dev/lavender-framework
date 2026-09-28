@props(['series' => [], 'labels' => [], 'height' => 280, 'unit' => '', 'title' => 'Chart', 'tableLabel' => 'Show as table'])

{{--
    Smooth area chart, server-rendered SVG: soft curves, a fading fill under
    each line, a round marker on the latest value.
    series: [['name' => ..., 'color' => ..., 'values' => [..]], ...]; labels: one per value.
    Ships a data table so the numbers are readable without the graphic.
--}}
@php
    $n = count($labels);
    $all = array_merge(...array_map(fn ($s) => $s['values'], $series));
    $lo = min(0, floor(min($all) / 5) * 5);
    $hi = max(ceil(max($all) / 5) * 5, $lo + 1);
    [$W, $H, $L, $R, $T, $B] = [720, $height, 40, 16, 12, 28];
    $x = fn (int $i) => $L + ($n > 1 ? $i * ($W - $L - $R) / ($n - 1) : 0);
    $y = fn (float $v) => $T + ($hi - $v) * ($H - $T - $B) / ($hi - $lo);
    $step = $hi - $lo > 12 ? 5 : ($hi - $lo > 6 ? 2 : 1);
    // Horizontal-tangent cubic between points: smooth, never overshoots the data.
    $line = function (array $values) use ($x, $y): string {
        $d = sprintf('M%.1f %.1f', $x(0), $y($values[0]));
        for ($i = 1; $i < count($values); $i++) {
            $mid = ($x($i - 1) + $x($i)) / 2;
            $d .= sprintf(' C%.1f %.1f %.1f %.1f %.1f %.1f', $mid, $y($values[$i - 1]), $mid, $y($values[$i]), $x($i), $y($values[$i]));
        }

        return $d;
    };
    $base = $y(max($lo, 0));
    $uid = 'dk-chart-'.substr(md5($title), 0, 6);
    $summary = $title.': '.implode(', ', array_map(fn ($s) => $s['name'].' latest '.end($s['values']).$unit, $series));
@endphp

<figure {{ $attributes->class(['m-0 grid gap-5']) }}>
    @if (count($series) > 1)
        <ul class="flex flex-wrap gap-5 text-sm text-dim">
            @foreach ($series as $s)
                <li class="flex items-center gap-2"><span class="size-2.5 rounded-full" style="background: {{ $s['color'] }}" aria-hidden="true"></span>{{ $s['name'] }}</li>
            @endforeach
        </ul>
    @endif

    <svg viewBox="0 0 {{ $W }} {{ $H }}" class="dk-chart h-auto w-full overflow-visible" role="img" aria-label="{{ $summary }}">
        <defs>
            @foreach ($series as $s)
                <linearGradient id="{{ $uid }}-{{ $loop->index }}" x1="0" x2="0" y1="0" y2="1">
                    <stop offset="0" style="stop-color: {{ $s['color'] }}; stop-opacity: 0.28" />
                    <stop offset="1" style="stop-color: {{ $s['color'] }}; stop-opacity: 0" />
                </linearGradient>
            @endforeach
        </defs>
        @for ($t = $lo; $t <= $hi; $t += $step)
            <line x1="{{ $L }}" x2="{{ $W - $R }}" y1="{{ $y($t) }}" y2="{{ $y($t) }}" style="stroke: var(--color-line-soft)" />
            <text x="{{ $L - 8 }}" y="{{ $y($t) + 4 }}" text-anchor="end">{{ $t }}{{ $unit }}</text>
        @endfor
        @foreach ($labels as $i => $label)
            @if ($i % 3 === 0 || $i === $n - 1)
                <text x="{{ $x($i) }}" y="{{ $H - 6 }}" text-anchor="middle">{{ $label }}</text>
            @endif
        @endforeach
        @foreach ($series as $s)
            @php($d = $line($s['values']))
            <path d="{{ $d }} L{{ $x($n - 1) }} {{ $base }} L{{ $x(0) }} {{ $base }} Z" fill="url(#{{ $uid }}-{{ $loop->index }})" />
            <path d="{{ $d }}" fill="none" style="stroke: {{ $s['color'] }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            <circle cx="{{ $x($n - 1) }}" cy="{{ $y(end($s['values'])) }}" r="5" style="fill: {{ $s['color'] }}; stroke: var(--color-panel)" stroke-width="3" />
        @endforeach
    </svg>

    <details>
        <summary class="cursor-pointer text-sm text-faint transition-colors hover:text-body">{{ $tableLabel }}</summary>
        <div class="dk-table-wrap mt-3 max-h-80">
            <table class="dk-table">
                <thead>
                    <tr>
                        <th scope="col"></th>
                        @foreach ($series as $s)
                            <th scope="col" class="text-right">{{ $s['name'] }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($labels as $i => $label)
                        <tr>
                            <th scope="row" class="text-left">{{ $label }}</th>
                            @foreach ($series as $s)
                                <td class="tabular text-right">{{ $s['values'][$i] }}{{ $unit }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </details>
</figure>
