@extends('layouts.main')

@section('title', 'Detail Alat Test - ROOMING')

@section('header-title', 'Detail Alat Test')

@section('breadcrumbs')
  <div class="breadcrumb-item"><a href="{{ route('alat-test.index') }}">Alat Test Booking</a></div>
  <div class="breadcrumb-item active">Detail Alat Test Booking</div>
@endsection

@section('section-title', 'Detail Alat Test')

@section('section-lead')
  Informasi detail mengenai alat test booking laboratorium.
@endsection

@section('content')
  <div class="card">
    <div class="card-header">
      <h4>{{ $booking->user->name }}</h4>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-12">
          <table class="table table-striped">
            <tr>
                <th style="width: 11%">Nama</th>
                <td>{{ $booking->user->name }}</td>
            </tr>
            <tr>
                <th>Tanggal</th>
                <td>{{ date("d F Y", strtotime($booking->date)) }}</td>
            </tr>
            <tr>
                <th>Waktu</th>
                <td>{{ date("H:i", strtotime($booking->start_time)) }} - {{ date("H:i", strtotime($booking->end_time)) }}</td>
            </tr>
            <tr>
                <th>Tujuan</th>
                <td>{{ $booking->purpose }}</td>
            </tr>
            <tr>
                <th style="vertical-align: top;">Status</th>
                <td>
                  {{ $booking->status }}
                  <br /><br />
                  @if($booking->status === 'PENDING')
                    <a href="javascript:void(0);" data-id="{{ $booking->id }}" 
                        data-title="Setujui" data-body="Yakin setujui booking ini?" 
                        data-value="1" class="btn btn-primary" id="acc-btn">
                      Setujui
                    </a>
                    <a href="javascript:void(0);" data-id="{{ $booking->id }}" 
                        data-title="Tolak" data-body="Yakin tolak booking ini?" 
                        data-value="0" class="btn btn-danger" id="deny-btn">
                      Tolak
                    </a>
                  @elseif($booking->status === 'DISETUJUI')
                    <a href="javascript:void(0);" data-id="{{ $booking->id }}" 
                        data-title="Tolak" data-body="Yakin tolak booking ini?" 
                        data-value="0" class="btn btn-danger" id="deny-btn">
                      Tolak
                    </a>

                    <a href="javascript:void(0);" data-id="{{ $booking->id }}" 
                      data-title="Terima" data-body="Pastikan barang sudah diterima!" 
                      data-value="2" class="btn btn-info" id="receive-btn">
                    Terima Alat Test
                  </a>
                  @endif
                </td>
            </tr>
          </table>
          
          <br />

          <table class="table table-sm" style="margin-left: 21px;">
            <tr>
                <th style="width: 3%">No.</th>
                <th>Alat</th>
                <th>Serial Number</th>
            </tr>
            @forelse ($booking->alatTestItemBooking as $key => $item)
              <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $item->alatTestItem->alatTest->name }}</td>
                <td>{{ $item->alatTestItem->serial_number }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="3">Tidak ada alat test.</td>
              </tr>
            @endforelse
          </table>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('after-script')
  <script>
    $(document).ready(function() {
      $(document).on('click', '#acc-btn, #deny-btn, #receive-btn', function() {
        let id = $(this).data('id');
        let title = $(this).data('title');
        let body = $(this).data('body');
        let value = $(this).data('value');
        let submitClass = value === 1 ? 'btn btn-primary' : 'btn btn-danger';

        $('.modal-title').html(title);
        $('.modal-body').html(body);
        $('#confirm-form').attr('action', `/admin/alat-test-booking-list/${id}/update/${value}`);
        $('#confirm-form').attr('method', 'POST');
        $('#submit-btn').attr('class', submitClass);
        $('#lara-method').attr('value', 'put');
        $('#confirm-modal').modal('show');
      });

      $(document).on('click', '[data-toggle="lightbox"]', function(e) {
        e.preventDefault();
        $(this).ekkoLightbox();
      });
    });
  </script>

  @include('includes.lightbox')
  @include('includes.notification')
  @include('includes.confirm-modal')
@endpush