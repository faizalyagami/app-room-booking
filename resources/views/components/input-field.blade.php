@if ($input_type != 'hidden')
  @if (isset($form_row) && $form_row == 'open')
    <div class="form-row">
  @endif

  <div class="form-group @isset($form_group_class) {{ $form_group_class }} @endisset">
    @isset($input_label)
      <label class="input-label">{{ $input_label }}</label>
    @endisset
@endif

{{-- Input Text, Number, Password, File, Hidden --}}
@if (in_array($input_type, ['text', 'number', 'password', 'file', 'hidden']))
  <input type="{{ $input_type }}"
    @isset($input_id) id="{{ $input_id }}" @endisset
    @isset($input_name) name="{{ $input_name }}" @endisset
    @isset($placeholder) placeholder="{{ $placeholder }}" @endisset
    class="form-control @isset($input_classes) {{ $input_classes }} @endisset @error($input_name) is-invalid @enderror"
    value="@isset($input_value){{ $input_value }}@else{{ ($input_type == 'password') ? '' : old($input_name) }}@endisset"
    @isset($other_attributes) {{ $other_attributes }} @endisset>

  @error($input_name)
    @include('includes.error-field')
  @enderror

  @isset($help_text)
    <small class="form-text text-muted">{{ $help_text }}</small>
  @endisset

{{-- Select Input --}}
@elseif ($input_type == 'select')
  <select
    @isset($input_id) id="{{ $input_id }}" @endisset
    @isset($input_name) name="{{ $input_name }}" @endisset
    class="form-control @isset($input_classes) {{ $input_classes }} @endisset @error($input_name) is-invalid @enderror"
    @isset($other_attributes) {{ $other_attributes }} @endisset
  >
    {!! $select_content !!}
  </select>

  @error($input_name)
    @include('includes.error-field')
  @enderror

{{-- Textarea --}}
@elseif ($input_type == 'textarea')
  <textarea
    @isset($input_id) id="{{ $input_id }}" @endisset
    @isset($input_name) name="{{ $input_name }}" @endisset
    class="form-control @isset($input_classes) {{ $input_classes }} @endisset @error($input_name) is-invalid @enderror"
    @isset($other_attributes) {{ $other_attributes }} @endisset
  >@isset($input_value){{ $input_value }}@else{{ old($input_name) }}@endisset</textarea>

  @error($input_name)
    @include('includes.error-field')
  @enderror
@endif

@if ($input_type != 'hidden')
    </div> {{-- close form-group --}}
    @if (isset($form_row) && $form_row == 'open')
      </div> {{-- close form-row --}}
    @endif
@endif
