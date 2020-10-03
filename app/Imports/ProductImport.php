<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ProductImport implements WithStartRow, WithChunkReading
{
    /**
    * @param Collection $collection
    */
    use Importable;

    //memulai pada baris kedua
    public function startRow(): int
    {
        return 2;
    }

    //untuk mengontrol penggunaan memori sekali proses
    public function chunkSize(): int
    {
        return 100;
    }
}
