<?php

namespace Database\Seeders;

use App\Models\AlatTest;
use App\Models\AlatTestItem;
use Illuminate\Database\Seeder;

class AlatTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dataSatuan = [
            'WBIS' => [
                '019/WBIS/LABPSI/2019',
                '028/WBIS/LABPSI/2019',
                '026/WBIS/LABPSI/2019',
                '018/WBIS/LABPSI/2019',
                '008/WBIS/LABPSI/2019',
                '022/WBIS/LABPSI/2019',
                '021/WBIS/LABPSI/2019',
                '015/WBIS/LABPSI/2019',
                '014/WBIS/LABPSI/2019',
                '006/WBIS/LABPSI/2019',
                '016/WBIS/LABPSI/2019',
                '002/WBIS/LABPSI/2019',
                '027/WBIS/LABPSI/2019',
                '023/WBIS/LABPSI/2019',
                '009/WBIS/LABPSI/2019',
                '006/WBIS/LABPSI/2019',
                '010/WBIS/LABPSI/2019',
                '027/WBIS/LABPSI/2025',
                '003/WBIS/LABPSI/2025',
                '033/WBIS/LABPSI/2025',
                '040/WBIS/LABPSI/2025',
                '028/WBIS/LABPSI/2025',
                '026/WBIS/LABPSI/2025',
                '025/WBIS/LABPSI/2025',
                '010/WBIS/LABPSI/2025',
                '009/WBIS/LABPSI/2025',
                '006/WBIS/LABPSI/2025',
                '008/WBIS/LABPSI/2025',
                '001/WBIS/LABPSI/2025',
                '004/WBIS/LABPSI/2025',
                '007/WBIS/LABPSI/2025',
                '020/WBIS/LABPSI/2025',
                '014/WBIS/LABPSI/2025',
                '016/WBIS/LABPSI/2025',
                '021/WBIS/LABPSI/2025',
                '002/WBIS/LABPSI/2025',
                '003/WBIS/LABPSI/2025',
            ],

            'WPPSI' => [
                '018/WPPSI/LABPSI/2019',
                '017/WPPSI/LABPSI/2019',
                '010/WPPSI/LABPSI/2019',
                '004/WPPSI/LABPSI/2019',
                '014/WPPSI/LABPSI/2019',
                '020/WPPSI/LABPSI/2019',
                '023/WPPSI/LABPSI/2019',
                '028/WPPSI/LABPSI/2019',
                '002/WPPSI/LABPSI/2019',
                '029/WPPSI/LABPSI/2019',
                '005/WPPSI/LABPSI/2019',
                '025/WPPSI/LABPSI/2019',
                '027/WPPSI/LABPSI/2019',
                '026/WPPSI/LABPSI/2019',
                '006/WPPSI/LABPSI/2019',
                '021/WPPSI/LABPSI/2019',
                '013/WPPSI/LABPSI/2019',
                '011/WPPSI/LABPSI/2019',
                '032/WPPSI/LABPSI/2019',
                '012/WPPSI/LABPSI/2019',
                '014/WPPSI/LABPSI/2025',
                '020/WPPSI/LABPSI/2025',
                '006/WPPSI/LABPSI/2025',
                '007/WPPSI/LABPSI/2025',
                '013/WPPSI/LABPSI/2025',
                '027/WPPSI/LABPSI/2025',
            ],
        ];

        foreach ($dataSatuan as $alatName => $serialNumbers) {
            $alat = AlatTest::firstOrCreate([
                'name' => $alatName,
            ], [
                'description' => null,
                'photo' => null,
            ]);

            foreach ($serialNumbers as $sn) {
                AlatTestItem::firstOrCreate([
                    'serial_number' => $sn,
                ], [
                    'alat_test_id' => $alat->id,
                    'type' => 1, //satuan
                    'quantity' => 1,
                    'status' => 'tersedia',
                ]);
            }
        }
        $dataLembar = [
            'RH Anak',
            'RH Biasa',
            'RH Klinis',
            'Lembar Jawab IST',
            'Lembar WZT',
            'Lembar RMIB',
            'Lembar Kerja Pauli',
            'Grafik Pauli',
            'Protokol Rorschach',
            'Lembar Inquiry Rorschach',
            'Protokol Stanford-Binnet',
            'Protokol WBIS',
            'Protokol WISC',
            'Protokol WPPSI',
            'Maze WPPSI',
            'Lembar CAT/TAT',
            'Lembar NST',
            'Lembar PM Colour',
            'Lembar EPPS',
            'Lembar MTS',
            'Lembar Forrer',
            'Penggaris Pauli',
            'Lembar Jawab Frostig',
            'Lembar SCCT',
            'Lembar Jawab FRT',
            'Lembar Jawab Holland',
            'Lembar Jawab Papi Kostick',
            'Lembar TMC',
            'Kartu Rorschach (Original-Switzerland)',
            'Kartu CAT (Original)',
            'Kartu TAT (Original)',
            'Kotak Stanford-Binnet (Manual)',
            'Kotak WISC',
            'Kotak WBIS',
            'Kotak WPPSI',
            'Buku Manual WBIS,WISC,WPPSI',
            'Buku Manual Stanford-Binnet',
            'Buku Manual PD I/PTP',
            'Buku Manual NST',
            'Buku EPPS',
            'Buku Papi Kostick',
            'Buku PM Colour',
            'Buku IST',
            'Buku Manual Frostig',
        ];
        foreach ($dataLembar as $name) {
            $alat = AlatTest::firstOrCreate(['name' => $name]);
            $serialName = str_replace(' ', '', $name);
            $serialName = preg_replace('/[^A-Za-z0-9]/', '', $serialName);
            $serial = "{$serialName}/LABPSI/001";

            AlatTestItem::firstOrCreate([
                'serial_number' => $serial,
            ], [
                'alat_test_id' => $alat->id,
                'type' => 2, // lembar
                'quantity' => 100, // jumlah lembar
                'status' => 'tersedia',
            ]);
        }
    }
}
