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
    <!-- FILTER SECTION -->
    <div class="card mb-4">
        <div class="card-header">
            <h4><i class="fas fa-filter"></i> Filter Data</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Filter Berdasarkan Hari</label>
                        <select id="filter-day" class="form-control">
                            <option value="">-- Semua Hari --</option>
                            <option value="Senin">Senin</option>
                            <option value="Selasa">Selasa</option>
                            <option value="Rabu">Rabu</option>
                            <option value="Kamis">Kamis</option>
                            <option value="Jumat">Jumat</option>
                            <option value="Sabtu">Sabtu</option>
                            <option value="Minggu">Minggu</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Filter Berdasarkan Ruangan</label>
                        <select id="filter-room" class="form-control">
                            <option value="">-- Semua Ruangan --</option>
                            @foreach ($rooms as $room)
                                <option value="{{ $room->name }}">{{ $room->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Filter Berdasarkan Tanggal</label>
                        <input type="date" id="filter-date" class="form-control" placeholder="Pilih Tanggal">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Filter Berdasarkan Status</label>
                        <select id="filter-status" class="form-control">
                            <option value="">-- Semua Status --</option>
                            <option value="PENDING">PENDING</option>
                            <option value="DISETUJUI">DISETUJUI</option>
                            <option value="DIGUNAKAN">DIGUNAKAN</option>
                            <option value="DITOLAK">DITOLAK</option>
                            <option value="SELESAI">SELESAI</option>
                            <option value="EXPIRED">EXPIRED</option>
                            <option value="BOOKING_BY_LAB">BOOKING_BY_LAB</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button id="btn-filter" class="btn btn-primary"><i class="fas fa-search"></i> Terapkan Filter</button>
                    <button id="btn-reset" class="btn btn-secondary"><i class="fas fa-undo"></i> Reset Filter</button>
                </div>
            </div>
        </div>
    </div>

    {{-- BULK DELETE SECTION --}}
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="form-group mb-0">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="select-all">
                            <label class="custom-control-label font-weight-bold" for="select-all">Pilih Semua</label>
                        </div>
                        <small class="text-muted ml-4">Hanya booking dengan status EXPIRED atau SELESAI yang dapat
                            dihapus</small>
                    </div>
                </div>
                <div class="col-md-6 text-right">
                    <button id="btn-delete-selected" class="btn btn-danger" disabled>
                        <i class="fas fa-trash"></i> Hapus Terpilih (<span id="selected-count">0</span>)
                    </button>
                </div>
            </div>
        </div>
    </div>

    @component('components.datatables')
        @slot('table_id', 'booking-list-table')
        @slot('table_header')
            <tr>
                <th style="width: 40px;">
                    <input type="checkbox" id="select-all-checkbox" style="display: none;">
                </th>
                <th>No</th>
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
            // Inisialisasi DataTable
            const table = $('#booking-list-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '/admin/booking-list/json',
                    data: function(d) {
                        d.filter_day = $('#filter-day').val();
                        d.filter_room = $('#filter-room').val();
                        d.filter_date = $('#filter-date').val();
                        d.filter_status = $('#filter-status').val();
                    }
                },
                order: [
                    [4, 'asc'],
                    [5, 'asc']
                ],
                columnDefs: [{
                        targets: [0],
                        orderable: false,
                        searchable: false,
                        width: '40px',
                        render: function(data, type, row) {
                            if (type === 'display') {
                                // Hanya tampilkan checkbox jika status EXPIRED atau SELESAI
                                if (row.status === 'EXPIRED' || row.status === 'SELESAI') {
                                    return `<input type="checkbox" class="row-checkbox" data-id="${row.id}" value="${row.id}">`;
                                }
                                return '';
                            }
                            return data;
                        }
                    },
                    {
                        targets: [1],
                        orderable: false,
                        searchable: false
                    },
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
                        targets: 7,
                        render: $.fn.dataTable.render.ellipsis(20, true)
                    },
                ],
                columns: [{
                        data: null,
                        defaultContent: ''
                    },
                    {
                        data: 'DT_RowIndex',
                        defaultContent: ''
                    },
                    {
                        data: 'room',
                        orderable: false,
                        render: function(data, type, row) {
                            let result = data || '-';
                            if (type === 'filter') return data ? data.toLowerCase() : '';

                            const now = new Date();
                            const dt = new Date(`${row.date}T${row.start_time}`);
                            result += '<div class="table-links">';

                            if (dt > now && (row.status === 'PENDING' || row.status ===
                                'DITOLAK')) {
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
                    {
                        data: 'user',
                        orderable: false
                    },
                    {
                        data: 'date_display',
                        name: 'date'
                    },
                    {
                        data: 'start_time'
                    },
                    {
                        data: 'end_time'
                    },
                    {
                        data: 'purpose'
                    },
                    {
                        data: 'status',
                        render: function(data) {
                            const badgeClass = {
                                'PENDING': 'warning',
                                'DISETUJUI': 'primary',
                                'DIGUNAKAN': 'info',
                                'DITOLAK': 'danger',
                                'EXPIRED': 'dark',
                                'BATAL': 'secondary',
                                'SELESAI': 'success',
                                'BOOKING_BY_LAB': 'warning',
                            } [data] || 'secondary';
                            return `<span class="badge badge-${badgeClass}">${data}</span>`;
                        }
                    },
                ],
                drawCallback: function() {
                    // Update checkbox "Pilih Semua" saat tabel di-render ulang
                    updateSelectAllState();
                    updateSelectedCount();
                }
            });

            // ===== FUNGSI CHECKBOX =====

            // Select All di header
            $('#select-all, #select-all-checkbox').on('change', function() {
                const isChecked = $(this).prop('checked');
                $('.row-checkbox:not(:disabled)').prop('checked', isChecked);
                updateSelectedCount();
            });

            // Event listener untuk checkbox per baris
            $(document).on('change', '.row-checkbox', function() {
                updateSelectedCount();
                updateSelectAllState();
            });

            // Update jumlah yang dipilih
            function updateSelectedCount() {
                const count = $('.row-checkbox:checked').length;
                $('#selected-count').text(count);
                $('#btn-delete-selected').prop('disabled', count === 0);
            }

            // Update state checkbox "Pilih Semua"
            function updateSelectAllState() {
                const total = $('.row-checkbox:not(:disabled)').length;
                const checked = $('.row-checkbox:checked').length;

                if (total === 0) {
                    $('#select-all').prop('checked', false).prop('indeterminate', false);
                    $('#select-all-checkbox').prop('checked', false).prop('indeterminate', false);
                    return;
                }

                if (checked === total) {
                    $('#select-all').prop('checked', true).prop('indeterminate', false);
                    $('#select-all-checkbox').prop('checked', true).prop('indeterminate', false);
                } else if (checked === 0) {
                    $('#select-all').prop('checked', false).prop('indeterminate', false);
                    $('#select-all-checkbox').prop('checked', false).prop('indeterminate', false);
                } else {
                    $('#select-all').prop('checked', false).prop('indeterminate', true);
                    $('#select-all-checkbox').prop('checked', false).prop('indeterminate', true);
                }
            }

            // ===== HAPUS MASSAL =====
            $('#btn-delete-selected').on('click', function() {
                const selectedIds = [];
                $('.row-checkbox:checked').each(function() {
                    selectedIds.push($(this).data('id'));
                });

                if (selectedIds.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Tidak ada data dipilih',
                        text: 'Silakan pilih booking yang ingin dihapus.'
                    });
                    return;
                }

                // Konfirmasi hapus
                Swal.fire({
                    title: 'Hapus Booking Terpilih?',
                    text: `Anda akan menghapus ${selectedIds.length} booking yang sudah ${selectedIds.length > 1 ? 'berakhir' : 'berakhir'}. Tindakan ini tidak dapat dibatalkan.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteSelectedBookings(selectedIds);
                    }
                });
            });

            function deleteSelectedBookings(ids) {
                // Tampilkan loading
                Swal.fire({
                    title: 'Menghapus data...',
                    text: 'Mohon tunggu sebentar.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '/admin/booking-list/delete-multiple',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        ids: ids
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message || `${ids.length} booking berhasil dihapus.`,
                            timer: 3000,
                            showConfirmButton: false
                        });
                        // Reload tabel
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        let errorMsg = 'Terjadi kesalahan saat menghapus data.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: errorMsg
                        });
                    }
                });
            }

            // ===== FILTER =====
            $('#btn-filter').on('click', function() {
                table.ajax.reload();
            });

            $('#btn-reset').on('click', function() {
                $('#filter-day').val('');
                $('#filter-room').val('');
                $('#filter-date').val('');
                $('#filter-status').val('');
                table.ajax.reload();
            });

            // ===== SETUJUI / TOLAK =====
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

            // Lightbox
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
