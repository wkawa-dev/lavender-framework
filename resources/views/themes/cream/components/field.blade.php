@props(['name', 'label', 'type' => 'text', 'optional' => null, 'rows' => null])

{{-- Label + control + server error. Extra attributes land on the control. --}}
<div>
    <label for="{{ $name }}" class="label">{{ $label }} @if ($optional)<span class="text-muted">{{ $optional }}</span>@endif</label>
    @if ($type === 'textarea')
        <textarea id="{{ $name }}" name="{{ $name }}" rows="{{ $rows ?? 5 }}" {{ $attributes->class(['field']) }} @error($name) aria-invalid="true" aria-describedby="{{ $name }}-error" @enderror>{{ old($name) }}</textarea>
    @else
        <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" value="{{ old($name) }}" {{ $attributes->class(['field']) }} @error($name) aria-invalid="true" aria-describedby="{{ $name }}-error" @enderror>
    @endif
    @error($name)<p id="{{ $name }}-error" class="mt-1.5 text-sm text-red-800">{{ $message }}</p>@enderror
</div>
