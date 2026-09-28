@props(['name', 'label', 'type' => 'text', 'optional' => null, 'rows' => null])

{{-- Micro label + control + server error. Extra attributes land on the control. --}}
<div>
    <label for="{{ $name }}" class="px-label">{{ $label }} @if ($optional)<span class="normal-case tracking-normal">{{ $optional }}</span>@endif</label>
    @if ($type === 'textarea')
        <textarea id="{{ $name }}" name="{{ $name }}" rows="{{ $rows ?? 5 }}" {{ $attributes->class(['px-field']) }} @error($name) aria-invalid="true" aria-describedby="{{ $name }}-error" @enderror>{{ old($name) }}</textarea>
    @else
        <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" value="{{ old($name) }}" {{ $attributes->class(['px-field']) }} @error($name) aria-invalid="true" aria-describedby="{{ $name }}-error" @enderror>
    @endif
    @error($name)<p id="{{ $name }}-error" class="mt-1.5 text-xs text-signal">{{ $message }}</p>@enderror
</div>
