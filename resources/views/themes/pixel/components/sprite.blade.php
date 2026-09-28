@props(['rows' => [], 'label' => null])

{{-- ASCII bitmap as a pixel grid: '.' empty, '1' grey, '2' orange. Aspect follows the bitmap. --}}
@php
    $fill = ['1' => 'var(--color-hy)', '2' => 'var(--color-frontier)'];
    $cols = max(1, strlen($rows[0] ?? '.'));
    $lines = max(1, count($rows));
@endphp
<span
    @if ($label) role="img" aria-label="{{ $label }}" @else aria-hidden="true" @endif
    {{ $attributes->class(['grid gap-px']) }}
    style="grid-template-columns: repeat({{ $cols }}, minmax(0, 1fr)); aspect-ratio: {{ $cols }} / {{ $lines }}"
>
    @foreach (str_split(implode('', $rows)) as $cell)
        <span @isset($fill[$cell]) style="background: {{ $fill[$cell] }}" @endisset></span>
    @endforeach
</span>
