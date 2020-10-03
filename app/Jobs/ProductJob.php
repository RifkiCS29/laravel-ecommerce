<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Imports\ProductImport; 
use Illuminate\Support\Str;
use App\Product;
use File;

class ProductJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $category;
    protected $filename;

  	//KARENA DISPATCH MENGIRIMKAN 2 PARAMETE, MAKA KITA TERIMA KEDUA DATA TERSEBUT
    public function __construct($category, $filename)
    {
        $this->category = $category;
        $this->filename = $filename;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      	//IMPORT DATA EXCEL TADI YANG SUDAH DISIMPAN DI STORAGE, KEMUDIAN DIKONVERSI MENJADI ARRAY
          $files = (new ProductImport)->toArray(storage_path('app/public/uploads/' . $this->filename));

        foreach ($files[0] as $row) {
          
            $explodeURL = explode('/', $row[4]);
            $explodeExtension = explode('.', end($explodeURL));
            $filename = time() . Str::random(6) . '.' . end($explodeExtension);
          
          	//download gambar
            file_put_contents(storage_path('app/public/products') . '/' . $filename, file_get_contents($row[4]));

            Product::create([
                'name' => $row[0],
                'slug' => $row[0],
                'category_id' => $this->category,
                'description' => $row[1],
                'price' => $row[2],
                'weight' => $row[3],
                'image' => $filename,
                'status' => true
            ]);
        }
      	//JIKA PROSESNYA SUDAH SELESAI MAKA FILE YANG ADA DISTORAGE AKAN DIHAPUS
        File::delete(storage_path('app/public/uploads/' . $this->filename));
    }
}
