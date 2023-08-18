<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LogExport implements FromArray, WithHeadings
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        // Return your data array here
        return $this->data;
    }

    public function headings(): array
    {
        // Define your header rows here
        return [
            ['-', '-', '-', '-', '-', '-', '-', '-', '-', 'Diambil dari compare capture suhu vs standard < 37,3', 'Diambil dengan kondisi jika salah satu kumis atau jenggot tidak NOK maka hasilnya NOK, untuk OK harus keduaya OK'],
            ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11'],
            ['Tanggal Absen', 'Jam Absen', 'Nik', 'Nama Karyawan', 'Nama Dept', 'Status Kumis', 'Status Jenggot', 'Suhu', 'Komitmen', 'Kondisi Sehat', 'Kondisi GMP'],
        ];
    }
}
