@php
    $selectedValue = old($input_name, $selected ?? null);
@endphp

<div class="form-group {{ $form_group_class ?? '' }}">
    <label for="{{ $input_name }}">{{ $input_label }}</label>
    <select 
        name="{{ $input_name }}" 
        id="{{ $input_name }}" 
        class="form-control {{ $input_class ?? '' }}" 
        {{ $other_attributes ?? '' }}
    >
        @foreach ($options as $value => $label)
            <option value="{{ $value }}" {{ $value == $selectedValue ? 'selected' : '' }}>
                {{ $label }}
            </option>
        @endforeach
    </select>
</div>
