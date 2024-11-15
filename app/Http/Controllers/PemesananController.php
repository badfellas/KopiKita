<?php

namespace App\Http\Controllers;

use App\Exports\PemesananExport;
use App\Models\DetailPemesanan;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Pemesanan;
use Maatwebsite\Excel\Facades\Excel;

class PemesananController extends Controller
{
    public function index()
    {
        $ingfo_sakkarepmu = 'List Pemesanan';
        $pemesanans = Pemesanan::first();
        $produks = Produk::select('id', 'kode_produk', 'nama_produk')->get();
        $users = User::all();
        return view('panel.pemesanan.index', [
            'ingfo_sakkarepmu' => $ingfo_sakkarepmu,
            'pemesanan' => $pemesanans,
            'users' => $users,
            'produks' => $produks,
        ]);
    }

    public function create()
    {
        return view('pemesanan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_pesanan' => 'required',
            'user_id' => 'required',
            'tanggal' => 'required',
            'status' => 'required|string',
            'produk_id' => 'required',
            'jumlah' => 'required|numeric|min:1',
        ]);

        $produk = Produk::findOrFail($request->produk_id);
        $hargaProduk = $produk->harga;

        if ($produk->stock < $request->jumlah) {
            return redirect()->back()->with('gagal', 'Stok produk tidak mencukupi.');
        }

        $pemesanan = new Pemesanan();
        $pemesanan->kode_pesanan = $request->kode_pesanan;
        $pemesanan->user_id = $request->user_id;
        $pemesanan->tanggal = $request->tanggal;
        $pemesanan->status = (string) $request->status;
        $pemesanan->save();

        $detail_pemesanan = new DetailPemesanan();
        $detail_pemesanan->pemesanan_id = $pemesanan->id;
        $detail_pemesanan->produk_id = $request->produk_id;
        $detail_pemesanan->jumlah = $request->jumlah;
        $detail_pemesanan->subtotal = $hargaProduk * $request->jumlah;
        $detail_pemesanan->save();

        $produk->stock -= $request->jumlah;
        $produk->save();

        return redirect()->route('pemesanan.index')->with('success', 'Pesanan berhasil ditambahkan.');
    }

    public function show($id)
    {
        $pemesanan = Pemesanan::find($id);
        if (!$pemesanan) {
            return abort(404);
        }

        $totalBarangDipesan = DetailPemesanan::where('pemesanan_id', $pemesanan->id)->sum('jumlah');
        $detail_pemesanan = DetailPemesanan::where('pemesanan_id', $id)->first();

        $ingfo_sakkarepmu = 'Detail Pesanan';
        $user = User::all();
        $produk = Produk::all();

        return view('panel.pemesanan.show', [
            'ingfo_sakkarepmu' => $ingfo_sakkarepmu,
            'pemesanan' => $pemesanan,
            'users' => $user,
            'detail_pemesanan' => $detail_pemesanan,
            'totalBarangDipesan' => $totalBarangDipesan,
            'produks' => $produk
        ]);
    }

    public function edit($id)
    {
        $pemesanan = Pemesanan::findOrFail($id);
        return view('pemesanan.edit', compact('pemesanan'));
    }

    public function update(Request $request, $id)
    {
        // Validasi input status
        $request->validate([
            'user_id' => 'required',
            'tanggal' => 'required',
            'status' => 'required|string',
        ]);

        // Daftar status yang valid
        $validStatuses = ['pending', 'selesai', 'batal']; // Sesuaikan dengan status yang valid
        if (!in_array($request->status, $validStatuses)) {
            return redirect()->back()->withErrors(['status' => 'Status tidak valid']);
        }

        $pemesanan = Pemesanan::findOrFail($id);
        $pemesanan->user_id = $request->user_id;
        $pemesanan->tanggal = $request->tanggal;
        $pemesanan->status = (string) $request->status;

        // Tambahkan aksi lain jika diperlukan ketika status menjadi "selesai"
        if ($request->status === 'selesai') {
            // Misalnya mengubah status lain, mengirim email, dll.
        }

        $pemesanan->save();
        return redirect()->route('pemesanan.index')->with('success', 'Pemesanan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $pemesanan = Pemesanan::findOrFail($id);
        $pemesanan->delete();
        return redirect()->route('pemesanan.index')->with('success', 'Pemesanan berhasil dihapus.');
    }

    public function getData(Request $request)
    {
        $pemesanans = Pemesanan::with('user');

        if ($request->ajax()) {
            return datatables()->of($pemesanans)
                ->addIndexColumn()
                ->addColumn('nama_user', function ($pemesanan) {
                    return $pemesanan->user->name;
                })
                ->addColumn('gambar_profile', function ($pemesanan) {
                    return $pemesanan->user->gambar_profile;
                })
                ->addColumn('actions', function ($pemesanan) {
                    return view('panel.pemesanan.actions', compact('pemesanan'));
                })
                ->toJson();
        }
    }

    public function exportExcel()
    {
        return Excel::download(new PemesananExport, 'pemesanan.xlsx');
    }
}
