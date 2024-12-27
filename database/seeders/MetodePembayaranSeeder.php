<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MetodePembayaran;

class MetodePembayaranSeeder extends Seeder
{
    public function run()
    {
        // Tambahkan metode pembayaran ke dalam tabel
        MetodePembayaran::create(['nama' => 'Bayar Di Kasir (Bisa Cash & QRIS)']);
    }
}
