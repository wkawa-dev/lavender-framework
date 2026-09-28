@props(['series' => [], 'labels' => [], 'height' => 280, 'unit' => '', 'title' => 'Chart', 'tableLabel' => 'Show as table'])

{{--
    Stepped line chart, server-rendered SVG: square joins, square end markers.
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
    $path = function (array $values) use ($x, $y): string {
        $d = '';
        foreach ($values as $i => $v) {
            $d .= $i === 0 ? sprintf('M%.1f %.1f', $x(0), $y($v)) : sprintf('H%.1f V%.1f', $x($i), $y($v));
        }

        return $d;
    };
    $summary = $title.': '.implode(', ', array_map(fn ($s) => $s['name'].' latest '.end($s['values']).$unit, $series));
@endphp

<figure {{ $attributes->class(['m-0 grid gap-4']) }}>
    @if (count($series) > 1)
        <ul class="flex flex-wrap gap-5 text-[13px] text-dim uppercase">
            @foreach ($series as $s)
                <li class="flex items-center gap-2"><span class="size-2.5" style="background: {{ $s['color'] }}" aria-hidden="true"></span>{{ $s['name'] }}</li>
            @endforeach
        </ul>
    @endif

    <svg viewBox="0 0 {{ $W }} {{ $H }}" class="px-chart h-auto w-full overflow-visible" role="img" aria-label="{{ $summary }}">
        @for ($t = $lo; $t <= $hi; $t += $step)
            @if ($t == 0)
                <line x1="{{ $L }}" x2="{{ $W - $R }}" y1="{{ $y($t) }}" y2="{{ $y($t) }}" stroke="#404040" stroke-dasharray="4 4" />
            @endif
            <text x="{{ $L - 8 }}" y="{{ $y($t) + 4 }}" text-anchor="end">{{ $t }}{{ $unit }}</text>
        @endfor
        @foreach ($labels as $i => $label)
            @if ($i % 3 === 0 || $i === $n - 1)
                <text x="{{ $x($i) }}" y="{{ $H - 6 }}" text-anchor="middle">{{ $label }}</text>
            @endif
        @endforeach
        @foreach ($series as $s)
            <path d="{{ $path($s['values']) }}" fill="none" stroke="{{ $s['color'] }}" stroke-width="3" stroke-linejoin="miter" stroke-linecap="square" />
            <rect x="{{ $x($n - 1) - 5 }}" y="{{ $y(end($s['values'])) - 5 }}" width="10" height="10" fill="{{ $s['color'] }}" />
        @endforeach
    </svg>

    <details>
        <summary class="cursor-pointer text-[13px] text-faint transition-colors hover:text-body">{{ $tableLabel }}</summary>
        <div class="px-table-wrap mt-3 max-h-80">
            <table class="px-table">
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
