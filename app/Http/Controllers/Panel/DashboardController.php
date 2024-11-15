<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Pembayaran;
use App\Models\Pemesanan;
use App\Models\Produk;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard dengan total jumlah produk.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Mengambil data pengguna yang baru saja dibuat dari database
        $userAnyar = User::latest()->take(7)->get(); // Ambil 5 pengguna terbaru

        // Mengambil data pemesanan dari database
        $pemesanan = Pemesanan::all();
        $totalBelumDibayar = Pembayaran::where('status', 'pending')->count();
        
        // Mengelompokkan data pemesanan berdasarkan status
        $pemesananPerHari = [];
        foreach ($pemesanan as $data) {
            $tanggal = $data->created_at->format('l');
            if (!isset($pemesananPerHari[$tanggal])) {
                $pemesananPerHari[$tanggal] = [
                    'pending' => 0,
                    'selesai' => 0,
                ];
            }

            // Menambah jumlah produk dari setiap status pemesanan pada hari tersebut
            switch ($data->status) {
                case 'pending':
                    $pemesananPerHari[$tanggal]['pending']++;
                    break;
                case 'selesai':
                    $pemesananPerHari[$tanggal]['selesai']++;
                    break;
            }
        }

        // Menyiapkan data untuk chart
        $dates = array_keys($pemesananPerHari);
        $pendingData = array_column($pemesananPerHari, 'pending');
        $selesaiData = array_column($pemesananPerHari, 'selesai');

        // Info produk
        $totalProduk = Produk::count();
        $barangBaruToday = Produk::whereDate('created_at', Carbon::today())->count();

        // Info pengguna
        $totalUser = User::count();
        $userBaruToday = User::whereDate('created_at', Carbon::today())->count();

        // Total pesanan
        $totalKabehPesanan = Pemesanan::count();
        $totalKabehPembayaran = Pembayaran::count();
        $totalPesananSelesai = Pemesanan::where('status', 'selesai')->count();
        $totalPemesanan = Pemesanan::count();

        $ingfo_sakkarepmu = "Dashboard";

        // Mengirimkan data ke view
        return view('panel.dashboard', [
            'totalProduk' => $totalProduk,
            'barangBaruToday' => $barangBaruToday,
            'totalUser' => $totalUser,
            'userBaruToday' => $userBaruToday,
            'totalKabehPesanan' => $totalKabehPesanan,
            'totalPesananSelesai' => $totalPesananSelesai,
            'totalPemesanan' => $totalPemesanan,
            'ingfo_sakkarepmu' => $ingfo_sakkarepmu,
            'pendingData' => $pendingData,
            'selesaiData' => $selesaiData,
            'dates' => $dates,
            'totalBelumDibayar' => $totalBelumDibayar,
            'totalKabehPembayaran' => $totalKabehPembayaran,
            'userAnyar' => $userAnyar, // Mengirimkan data pengguna baru ke view
        ])->with('success', 'Berhasil login.');
    }

    public function create()
    {
        $ingfo_sakkarepmu = 'Tambah Kategori';
        $produk = Kategori::all();
        $kategoris = Kategori::all();
        return view('panel.kategori.create', [
            'ingfo_sakkarepmu' => $ingfo_sakkarepmu,
            'produks' => $produk,
            'kategoris' => $kategoris,
        ]);
    }
}
