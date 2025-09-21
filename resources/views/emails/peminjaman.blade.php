@component('mail::message')
# Notifikasi Peminjaman Alat Test

Halo **{{ $peminjaman->user->name }}**,

Berikut detail peminjaman alat test Anda:

- **Alat Test** : {{ $peminjaman->alatTest->nama_alat }}
- **Serial Number** : {{ $peminjaman->alatTestUnit->serial_number ?? '-' }}
- **Tanggal Pinjam** : {{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d M Y') }}
- **Tanggal Kembali** : {{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali)->format('d M Y') }}
- **Status** : **{{ strtoupper($peminjaman->status) }}**

@component('mail::button', ['url' => url('/peminjaman/'.$peminjaman->id)])
Lihat Detail
@endcomponent

Terima kasih,  
{{ config('app.name') }}
@endcomponent
