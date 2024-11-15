<?php
use App\Http\Controllers\PesananController;
use App\Http\Controllers\API\KategoriController;
use App\Http\Controllers\API\ProdukController;
use App\Http\Controllers\API\SupplierController;
use App\Http\Controllers\DetailPemesananController;
use App\Http\Controllers\KeranjangController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PemesananController;
use App\Http\Controllers\PesanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SupermarketController;
use App\Http\Controllers\WhishlistController;
use App\Http\Middleware\MultiRoleMiddleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Default route
Route::get('/', [SupermarketController::class, 'index']);

// Middleware untuk superadmin dan admin
Route::middleware([MultiRoleMiddleware::class . ':superadmin,admin'])->group(function () {
    Route::get('panel', [\App\Http\Controllers\Panel\DashboardController::class, 'index'])->name('panel.dashboard');
    // Rute resource untuk produk
    Route::resource('/panel/produk', ProdukController::class);
    // Rute resource untuk kategori
    Route::resource('/panel/kategori', KategoriController::class);
    // Rute resource untuk supplier
    Route::resource('/panel/supplier', SupplierController::class);
    // Rute resource untuk pemesanan
    Route::resource('/panel/pemesanan', PemesananController::class);
    // Rute resource untuk pembayaran
    Route::resource('/panel/pembayaran', PembayaranController::class);

    Route::get('produk/exportExcel', [ProdukController::class, 'exportExcel'])->name('produk.exportExcel');
    Route::get('kategori/exportExcel', [KategoriController::class, 'exportExcel'])->name('kategori.exportExcel');
    Route::get('supplier/exportExcel', [SupplierController::class, 'exportExcel'])->name('supplier.exportExcel');
    Route::get('pemesanan/exportExcel', [PemesananController::class, 'exportExcel'])->name('pemesanan.exportExcel');
    Route::get('pembayaran/exportExcel', [PembayaranController::class, 'exportExcel'])->name('pembayaran.exportExcel');
});

// Middleware untuk superadmin saja
Route::middleware(['role:superadmin'])->group(function () {
    Route::get('panel/permission', function () {
        return view('panel.perms.index');
    })->name('perms.index');
});

Auth::routes();
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::post('/filter-produk', [SupermarketController::class, 'filterProduk'])->name('filter.produk');
Route::post('/filter-produk', [ProdukController::class, 'filterProduk'])->name('filter.produk');

// route gawe profile user
Route::middleware('auth')->group(function () {
    Route::get('/home', [App\Http\Controllers\SupermarketController::class, 'index'])->name('supermarket.index');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/history-pembelian', [ProfileController::class, 'pembelian'])->name('profile.pembelian');
    Route::get('/profile/edit/{id}', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update/{id}', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile/delete/{id}', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/updateAvatar', [ProfileController::class, 'updateAvatar'])->name('profile.updateAvatar');
    Route::delete('/profile/deleteAvatar', [ProfileController::class, 'deleteAvatar'])->name('profile.deleteAvatar');

    Route::resource('/keranjang', KeranjangController::class)->names('supermarket.keranjang');
    Route::post('/keranjang/tambah', [KeranjangController::class, 'tambahProduk'])->name('keranjang.tambah');
    Route::put('/keranjang/{keranjang}', [KeranjangController::class, 'update'])->name('keranjang.update');
    Route::delete('/keranjang/{keranjang}', [KeranjangController::class, 'destroy'])->name('keranjang.destroy');
    Route::get('/keranjang/{keranjang}/edit', [KeranjangController::class, 'edit'])->name('keranjang.edit');

    // Gawe pesanan user
    Route::resource('/pesanan', PesananController::class);
    Route::get('/pesanan', [PesanController::class, 'index'])->name('pesanan.index');
    Route::get('/pesanan/{id}', [PesanController::class, 'index'])->name('pesanan.show');
    Route::post('/pesan', [PesanController::class, 'pesan'])->name('pesan');
    Route::post('/pesanan/tambah', [PesanController::class, 'pesanBayar'])->name('pesanan.tambah');
    Route::get('/pesanan/{id}/edit', 'PesananController@edit')->name('pesanan.edit');
    Route::put('/pesanan/{id}', [PesanController::class, 'update'])->name('pesanan.update');
    Route::delete('/pesanan/{id}', [PesanController::class, 'destroy'])->name('pesanan.destroy');
    Route::post('/pesanan/payment', [PesanController::class, 'bayar'])->name('pesanan.payment');
    
    // Pembayaran QRIS
    Route::get('/keranjang/bayarqris', function () {
        return view('bayarqris');
    })->name('bayarqris');
    
    // Rute baru untuk bayar
    Route::post('/bayar/{id}', [PesanController::class, 'bayar'])->name('bayar');
});

// Rute untuk mendapatkan data produk dan kategori
Route::get('data/produk', [ProdukController::class, 'getData'])->name('produk.data');
Route::get('data/kategori', [KategoriController::class, 'getData'])->name('kategori.data');
Route::get('data/supplier', [SupplierController::class, 'getData'])->name('supplier.data');
Route::get('data/pemesanan', [PemesananController::class, 'getData'])->name('pemesanan.data');
Route::get('data/detail-pemesanan', [DetailPemesananController::class, 'getData'])->name('detail_pemesanan.data');
Route::get('data/wishlist', [WhishlistController::class, 'getData'])->name('wishlist.data');
Route::get('data/pembayaran', [PembayaranController::class, 'getData'])->name('pembayaran.data');
Route::get('data/keranjang', [KeranjangController::class, 'getData'])->name('keranjang.data');
Route::get('data/pesanan', [PesanController::class, 'getData'])->name('pesanan.data');
