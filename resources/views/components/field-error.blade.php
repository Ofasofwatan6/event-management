@props(['field'])

@error($field)
    <p class="text-red-600 text-xs font-semibold mt-1">{{ $message }}</p>
@enderror
