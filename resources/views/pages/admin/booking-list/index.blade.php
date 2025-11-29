@extends('layouts.main')

@section('title', 'Booking List - ROOMING')

@section('header-title', 'Booking List')

@section('breadcrumbs')
  <div class="breadcrumb-item"><a href="#">Transaksi</a></div>
  <div class="breadcrumb-item active">Booking List</div>
@endsection

@section('section-title', 'Booking List')

@section('section-lead')
  Berikut ini adalah daftar seluruh booking dari setiap user.
@endsection

@section('content')
  @component('components.datatables')
    @slot('table_id', 'booking-list-table')
    @slot('table_header')
      <tr>
        <th>No</th>
        <!-- <th>Foto</th> -->
        <th>Ruangan</th>
        <th>Nama</th>
        <th>Tanggal</th>
        <th>Waktu Mulai</th>
        <th>Waktu Selesai</th>
        <th>Keperluan</th> 
        <th>Status</th> 
      </tr>
    @endslot
  @endcomponent
@endsection

@push('after-script')
<script src="//cdn.datatables.net/plug-ins/1.10.22/dataRender/ellipsis.js"></script>

<script>
$(document).ready(function() {
  const table = $('#booking-list-table').DataTable({
    processing: true,
    serverSide: false,
    ajax: '{{ route('booking-list.json') }}',
    order: [],
    columnDefs: [
      {
        targets: [3],
        type: 'date',
        orderData: [3, 4]
      },
      {
        targets: [4],
        orderData: [4, 3]
      },
      {
        targets: 6,
        render: $.fn.dataTable.render.ellipsis(20, true)
      },
    ],

    // order: [[7, 'asc'], [3, 'asc'], [4, 'asc']], // Urut berdasar tanggal & jam mulai
    columns: [
      {
        data: 'index',
        orderable: false,
        searchable: false
      },
      // {
      //   data: 'photo',
      //   orderable: false,
      //   searchable: false,
      //   render: function (data) {
      //     if (data && data !== '-') {
      //       return `
      //         <div class="gallery gallery-fw">
      //           <a href="/storage/${data}" data-toggle="lightbox">
      //             <img src="/storage/${data}" class="img-fluid" style="min-width: 80px; height: auto;">
      //           </a>
      //         </div>`;
      //     }
      //     return '-';
      //   }
      // },
      {
        data: 'room',
        orderable: false,
        render: function (data, type, row) {
          let result = data || '-';

          if (type === 'filter') return data ? data.toLowerCase() : '';

          const now = new Date();
          const dt = new Date(`${row.date}T${row.start_time}`);
          result += '<div class="table-links">';

          if (dt > now && (row.status === 'PENDING' || row.status === 'DITOLAK')) {
            result += ` 
              <a href="javascript:;" data-id="${row.id}" 
                 data-title="Setujui" data-body="Yakin setujui booking ini?" 
                 data-value="1" class="text-primary" id="acc-btn">Setujui</a>`;
            if (row.status === 'PENDING') {
              result += '<div class="bullet"></div>';
            }
          }

          if (row.status === 'PENDING' || row.status === 'DISETUJUI') {
            result += ` 
              <a href="javascript:;" data-id="${row.id}" 
                 data-title="Tolak" data-body="Yakin tolak booking ini?" 
                 data-value="0" class="text-danger" id="deny-btn">Tolak</a>`;
          }

          result += '</div>';
          return result;
        }
      },
      { data: 'user', orderable: false },
      { data: 'date',
        render: function (data, type, row) {
          //Format tanggal dengan nama hari Indonesia
          if (data) {
            const dateObj = new Date(data + 'T00:00:00');
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const dayName = days[dateObj.getDay()];

            if (type === 'display') {
              return `${data} - ${dayName}`;
            }
            //untuk sorting dan filtering, kembalikan data asli
            return data || '-';
          }
        }
       },
      { data: 'start_time' },
      { data: 'end_time' },
      { data: 'purpose' },
      {
        data: 'status',
        render: function (data) {
          const badgeClass = {
            'PENDING': 'info',
            'DISETUJUI': 'primary',
            'DIGUNAKAN': 'primary',
            'DITOLAK': 'danger',
            'EXPIRED': 'dark',
            'BATAL': 'warning',
            'SELESAI': 'success',
            'BOOKING_BY_LAB': 'info',
          }[data] || 'secondary';
          const textClass = (data === 'BATAL' || data === 'BATAL') ? 'text-dark' : '';
          return `<span class="badge badge-${badgeClass} ${textClass}">${data}</span>`;
        }
      },
    ]
  });

  // ✅ Custom search — mendukung kata berurutan seperti “seminar 1” atau “lab psikologi 15”
  $.fn.dataTable.ext.search.push(function(settings, data) {
  const search = $('#booking-list-table_filter input').val().toLowerCase().trim();
  if (!search) return true;

  const room = (data[1] || '').toLowerCase();
  const user = (data[2] || '').toLowerCase();
  const date = (data[3] || '').toLowerCase();
  const start_time = (data[4] || '').toLowerCase();
  const end_time = (data[5] || '').toLowerCase();
  const purpose = (data[6] || '').toLowerCase();
  const status = (data[7] || '').toLowerCase();

  const pattern = new RegExp(search, 'i');
  return (
    pattern.test(room) ||
    pattern.test(user) ||
    pattern.test(date) ||
    pattern.test(start_time) ||
    pattern.test(end_time) ||
    pattern.test(purpose) ||
    pattern.test(status)
  );
});


  // ✅ Modal konfirmasi (Setujui/Tolak)
  $(document).on('click', '#acc-btn, #deny-btn', function() {
    const id = $(this).data('id');
    const title = $(this).data('title');
    const body = $(this).data('body');
    const value = $(this).data('value');
    const submitClass = value === 1 ? 'btn btn-primary' : 'btn btn-danger';

    $('.modal-title').html(title);
    $('.modal-body').html(body);
    $('#confirm-form').attr('action', `/admin/booking-list/${id}/update/${value}`);
    $('#confirm-form').attr('method', 'POST');
    $('#submit-btn').attr('class', submitClass);
    $('#lara-method').attr('value', 'put');
    $('#confirm-modal').modal('show');
  });

  // ✅ Lightbox gambar
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
