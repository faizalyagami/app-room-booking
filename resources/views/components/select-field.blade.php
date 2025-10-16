@php
    $selectedValue = old($input_name, $selected ?? null);
@endphp

<div class="form-group {{ $form_group_class ?? '' }}">
    <label>{{ $input_label ?? '' }}
        @if(isset($other_attributes) && str_contains($other_attributes, 'required'))
            <span class="text-danger">*</span>
        @endif
    </label>

    @php
        // Tentukan default 'USER' jika kosong/null
        $selectedValue = old($input_name, $selected ?? 'USER');
    @endphp

    <select name="{{ $input_name ?? '' }}" id="{{ $input_name ?? '' }}" class="form-control" {{ $other_attributes ?? '' }}>
        <option value="">-- Pilih {{ $input_label ?? 'Opsi' }} --</option>
        @foreach($options as $key => $value)
            <option value="{{ $key }}" {{ (string)$selectedValue === (string)$key ? 'selected' : '' }}>
                {{ $value }}
            </option>
        @endforeach
    </select>
</div>
