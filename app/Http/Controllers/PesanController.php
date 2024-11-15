<?php

namespace App\Http\Controllers;

use App\Models\DetailPemesanan;
use App\Models\Keranjang;
use App\Models\MetodePembayaran;
use App\Models\Pembayaran;
use App\Models\Pemesanan;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PesanController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $ingfo_sakkarepmu = 'List Pesanan Anda';
        $keranjang = Keranjang::where('user_id', $userId)->get();
        $pemesanans = Pemesanan::where('user_id', $userId)->get();
        $produks = Produk::select('id', 'kode_produk', 'nama_produk')->get();
        $users = User::all();
        $jumlahProdukKeranjang = $keranjang->count();
        $metode_pembayaran = MetodePembayaran::select('id', 'nama')->get();
        $jumlahPemesanan = $pemesanans->count();

        // Ambil total bayar dari tabel Pembayaran berdasarkan pemesanan
        $totalBayar = Pembayaran::whereIn('pemesanan_id', $pemesanans->pluck('id'))
            ->where('status', '!=', 'dibatalkan')
            ->sum('total');

        return view('supermarket.pesanan.index', [
            'ingfo_sakkarepmu' => $ingfo_sakkarepmu,
            'pemesanans' => $pemesanans,
            'keranjang' => $keranjang,
            'users' => $users,
            'produks' => $produks,
            'jumlahProdukKeranjang' => $jumlahProdukKeranjang,
            'jumlahPemesanan' => $jumlahPemesanan,
            'metode_pembayaran' => $metode_pembayaran,
            'totalBayar' => $totalBayar,
        ]);
    }

    public function show($id)
    {
        $pesanan = Pemesanan::findOrFail($id);
        return view('supermarket.pesanan.show', compact('pesanan'));
    }

    public function pesan(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'metode_pembayaran' => 'required', // pastikan metode pembayaran terpilih
            ]);

            // Generate random kode pemesanan
            $kode_pesanan = $this->generateRandomCodePemesanan();
            $user_id = Auth::id();

            // Simpan pemesanan ke database
            $pemesanan = new Pemesanan();
            $pemesanan->kode_pesanan = $kode_pesanan;
            $pemesanan->user_id = $user_id;
            $pemesanan->status = 'pending';
            $pemesanan->tanggal = now();
            $pemesanan->save();

            // Ambil semua item dalam keranjang pengguna
            $itemsKeranjang = Keranjang::where('user_id', $user_id)->get();

            $metode_pembayaran_id = $request->input('metode_pembayaran');
            // Hitung total bayar
            $totalBayar = $itemsKeranjang->sum(function ($item) {
                return $item->jumlah * $item->produk->harga;
            });

            Pembayaran::create([
                'pemesanan_id' => $pemesanan->id,
                'total' => $totalBayar,
                'metode_pembayaran_id' => $metode_pembayaran_id,
                'status' => 'pending',
            ]);

            // Simpan detail pemesanan untuk setiap item di keranjang
            foreach ($itemsKeranjang as $item) {
                $produk = Produk::find($item->produk_id);
                if ($produk) {
                    $subtotal = $item->jumlah * $produk->harga;

                    // Simpan detail pemesanan
                    $detailPemesanan = new DetailPemesanan();
                    $detailPemesanan->pemesanan_id = $pemesanan->id;
                    $detailPemesanan->produk_id = $item->produk_id;
                    $detailPemesanan->jumlah = $item->jumlah;
                    $detailPemesanan->subtotal = $subtotal;
                    $detailPemesanan->save();

                    // Kurangi stok produk
                    $produk->stock -= $item->jumlah;
                    $produk->save();
                }
            }

            // Hapus semua item dalam keranjang
            Keranjang::where('user_id', $user_id)->delete();

            // Commit transaksi
            DB::commit();

            // Berikan respons ke frontend
            return redirect()->back()->with('success', 'Pesanan berhasil ditambahkan!');
        } catch (\Exception $e) {
            // Rollback jika ada kesalahan
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan. Pesanan gagal ditambahkan.');
        }
    }

    public function pesanBayar(Request $request)
    {
        // Validasi input
        $request->validate([
            'pembayaran_id' => 'required|exists:pembayaran,id', // Pastikan ID pembayaran valid
        ]);

        // Temukan pembayaran dan ubah statusnya
        $pembayaran = Pembayaran::find($request->pembayaran_id);
        $pembayaran->status = 'dibayar'; // Ubah status pembayaran
        $pembayaran->save();

        // Respons berhasil
        return response()->json(['success' => true, 'message' => 'Status pembayaran berhasil diubah']);
    }

    private function generateRandomCodePemesanan()
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $result = '';
        for ($i = 0; $i < 3; $i++) {
            for ($j = 0; $j < 3; $j++) {
                $result .= $characters[rand(0, strlen($characters) - 1)];
            }
            if ($i < 2) {
                $result .= '-'; // Tambahkan tanda hubung setelah setiap kelompok
            }
        }
        return $result;
    }

    public function getData(Request $request)
    {
        $userId = Auth::id();
        $pemesanans = Pemesanan::with(['user', 'metode_pembayaran'])
            ->where('user_id', $userId)
            ->get();

        if ($request->ajax()) {
            return datatables()->of($pemesanans)
                ->addIndexColumn()
                ->addColumn('nama_user', function ($pemesanan) {
                    return $pemesanan->user->name;
                })
                ->addColumn('gambar_profile', function ($pemesanan) {
                    return $pemesanan->user->gambar_profile;
                })
                ->addColumn('metode_pembayaran', function ($pemesanan) {
                    return $pemesanan->metode_pembayaran ? $pemesanan->metode_pembayaran->nama : 'Metode Pembayaran Tidak Diketahui';
                })
                ->addColumn('total_bayar', function ($pemesanan) {
                    return DetailPemesanan::where('pemesanan_id', $pemesanan->id)->sum('subtotal');
                })
                ->addColumn('status_bayar', function ($pemesanan) {
                    $status_bayar = Pembayaran::where('pemesanan_id', $pemesanan->id)->first()->status ?? 'pending';
                    return ucfirst($status_bayar);
                })
                ->addColumn('actions', function ($pesanan) {
                    return view('supermarket.pesanan.actions', compact('pesanan'));
                })
                ->toJson();
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $pesanan = Pemesanan::findOrFail($id);
        $pesanan->name = $request->name;
        $pesanan->save();
        return redirect()->route('pesanan.index', ['id' => $pesanan->id])->with('success', 'Pesanan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $pesanan = Pemesanan::findOrFail($id);
        $pesanan->delete();

        return redirect()->route('pesanan.index')->with('success', 'Pesanan berhasil dihapus.');
    }

    public function payment()
    {
        return view('pesanan.payment');
    }

    public function bayar()
    {
        // Mendapatkan pembayaran yang sesuai untuk pengguna yang sedang login
        $pesanan = Pembayaran::whereHas('pemesanan', function ($query) {
            $query->where('user_id', auth()->id());
        })->where('status', 'pending')->first();

        if ($pesanan) {
            // Update status pembayaran menjadi "diproses"
            $pesanan->update(['status' => 'diproses']);

            return redirect()->route('pesanan.index')->with('success', 'Pembayaran berhasil dilakukan.');
        }

        return redirect()->route('pesanan.index')->with('error', 'Pembayaran tidak dapat diproses.');
    }
}
