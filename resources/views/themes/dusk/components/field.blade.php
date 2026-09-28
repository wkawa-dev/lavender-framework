@props(['name', 'label', 'type' => 'text', 'optional' => null, 'rows' => null])

{{-- Label + control + server error. Extra attributes land on the control. --}}
<div>
    <label for="{{ $name }}" class="dk-label">{{ $label }} @if ($optional)<span class="font-normal text-faint">{{ $optional }}</span>@endif</label>
    @if ($type === 'textarea')
        <textarea id="{{ $name }}" name="{{ $name }}" rows="{{ $rows ?? 5 }}" {{ $attributes->class(['dk-field']) }} @error($name) aria-invalid="true" aria-describedby="{{ $name }}-error" @enderror>{{ old($name) }}</textarea>
    @else
        <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" value="{{ old($name) }}" {{ $attributes->class(['dk-field']) }} @error($name) aria-invalid="true" aria-describedby="{{ $name }}-error" @enderror>
    @endif
    @error($name)<p id="{{ $name }}-error" class="mt-2 text-sm text-rose">{{ $message }}</p>@enderror
</div>
