@props(['items' => []])

{{-- Stat strip. items: [['label' => ..., 'value' => ...], ...] --}}
<dl {{ $attributes->class(['stats']) }}>
    @foreach ($items as $item)
        <div>
            <dt class="micro">{{ $item['label'] }}</dt>
            <dd>{{ $item['value'] }}</dd>
        </div>
    @endforeach
</dl>
