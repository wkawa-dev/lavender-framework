@props(['items' => []])

{{-- Stat strip: serif values over small labels. items: [['label' => ..., 'value' => ...], ...] --}}
<dl {{ $attributes->class(['stats']) }}>
    @foreach ($items as $item)
        <div class="flex flex-col-reverse">
            <dt>{{ $item['label'] }}</dt>
            <dd>{{ $item['value'] }}</dd>
        </div>
    @endforeach
</dl>
