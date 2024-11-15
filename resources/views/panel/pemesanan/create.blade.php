<form action="{{ route('pemesanan.store') }}" method="POST" enctype="multipart/form-data" id="formTambahData">
    @csrf
    <div class="modal modal-blur fade" id="modal-tambahPesanan" tabindex="-1" role="dialog" aria-hidden="true"
        data-bs-backdrop="static">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Data Pesanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Nama Pemesan</label>
                                <select class="form-select" id="select-username" name="user_id">
                                    <option value="" disabled selected>Username</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            data-custom-properties="<span class='avatar avatar-xs' style='background-image: url({{ $user->gambar_profile ? asset('storage/' . $user->gambar_profile) : '' }})'></span>"
                                            {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Kode Produk</label>
                                <input type="text" class="form-control @error('kode_pesanan') is-invalid @enderror"
                                    name="kode_pesanan" id="kode_pesanan" value="{{ old('kode_pesanan') }}" readonly
                                    disabled>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Pemesanan</label>
                                <input type="date" class="form-control @error('tanggal') is-invalid @enderror"
                                    name="tanggal" id="tanggal" value="{{ old('tanggal') }}" required>
                                @error('tanggal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-6">
                                <label class="form-label">Nama Produk</label>
                                <select class="form-select" id="produk_id" name="produk_id">
                                    <option value="" disabled selected>Pilih Produk</option>
                                    @foreach ($produks as $produk)
                                        <option value="{{ $produk->id }}"
                                            data-custom-properties="<span class='badge bg-primary-lt'>{{ $produk->kode_produk }}</span>{{ $produk->nama_produk }}"
                                            {{ old('produk_id') == $produk->id ? 'selected' : '' }}>
                                            {{ $produk->kode_pesanan . ' ' . $produk->nama_produk }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('produk_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Status Pemesanan</label>
                                <select class="form-select @error('status') is-invalid @enderror" name="status"
                                    id="status">
                                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="selesai" {{ old('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Jumlah Pesanan</label>
                                <input class="form-control @error('jumlah') is-invalid @enderror" type="number"
                                    name="jumlah" id="jumlah" value="{{ old('jumlah') }}" placeholder="1">
                                @error('jumlah')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#" class="btn btn-link link-secondary" data-bs-dismiss="modal">Cancel</a>
                    <div class="col-auto ms-auto d-print-none">
                        <div class="btn-list">
                            <span class="d-none d-sm-inline">
                                <a class="btn" onclick="resetForm()">Reset</a>
                            </span>
                            <button type="submit" class="btn btn-primary d-none d-sm-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M12 5l0 14" />
                                    <path d="M5 12l14 0" />
                                </svg>
                                Simpan
                            </button>
                            <button type="submit" class="btn btn-primary d-sm-none btn-icon" aria-label="Tambah Produk">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M12 5l0 14" />
                                    <path d="M5 12l14 0" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    // JS untuk mengatur TomSelect di dropdown Username dan Produk
    document.addEventListener("DOMContentLoaded", function() {
        window.TomSelect && (new TomSelect(document.getElementById('select-username')));
        window.TomSelect && (new TomSelect(document.getElementById('produk_id')));
    });
</script>

<script>
    // Tampilkan modal jika ada error
    jQuery(document).ready(function($) {
        @if ($errors->any())
            $('#modal-tambahPesanan').modal('show');
        @endif
    });
</script>

<script>
    // Fungsi simpan dan reset form
    function resetForm() {
        document.getElementById("nama_user").value = '';
        document.getElementById("kategori_id").value = '';
        document.getElementById("harga").value = '';
        document.getElementById("deskripsi").value = '';
        Swal.fire({
            icon: "success",
            title: 'Berhasil di Reset',
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    };
</script>
