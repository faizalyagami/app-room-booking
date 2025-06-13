<div class="card card-statistic-1">
  <div class="card-icon @isset($bg_color) {{ $bg_color }} @endisset">
    <i class="@isset($icon) {{ $icon }} @endisset"></i>
  </div>
  <div class="card-wrap">
    <div class="card-header">
      <h4>@isset($title) {{ $title }} @endisset</h4>
    </div>
    <div class="card-body">
      @isset($value) {{ $value }} @endisset
    </div>
  </div>
</div>

{{-- <div class="card card-statistic-1">
  <div class="card-icon {{ $bg_color }}">
    <i class="{{ $icon }}"></i>
  </div>
  <div class="card-wrap">
    <div class="card-header">
      <h4>{{ $title }}</h4>
    </div>
    <div class="card-body">
      {{ $value }}
    </div>
  </div>
</div> --}}