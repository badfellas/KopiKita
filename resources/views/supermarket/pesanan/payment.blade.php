<form action="{{ route('pesanan.tambah') }}" method="POST" enctype="multipart/form-data" id="formTambahData">
    @csrf
    <div class="modal modal-blur fade" id="modal-methodBayar" tabindex="-1" role="dialog" aria-hidden="true"
        data-bs-backdrop="static">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pilih Metode Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label class="form-label">Metode Pembayaran</label>
                                <select class="form-select @error('metode_pembayaran') is-invalid @enderror"
                                    name="metode_pembayaran" id="metode_pembayaran">
                                    <option value="">Pilih Metode Pembayaran</option>
                                    <option value="qris">Bayar menggunakan QRIS</option>
                                    <option value="cash">Bayar Di Kasir (Cash)</option>
                                </select>
                                @error('metode_pembayaran')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <!-- Image QRIS Barcode -->
                    <div id="qris-barcode-container" style="display: none; text-align: center; margin-top: 20px;">
                    <img src="{{ asset('storage/img/icon/qris-barcode.png') }}" alt="QRIS Barcode" style="max-width: 200px;">
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#" class="btn btn-link link-secondary" data-bs-dismiss="modal">
                        Cancel
                    </a>
                    <div class="col-auto ms-auto d-print-none">
                        <div class="btn-list">
                            <span class="d-none d-sm-inline">
                                <a class="btn" onclick="resetForm()">
                                    Reset
                                </a>
                            </span>
                            <form action="{{ route('pesan.bayar', ['id' => $pesanan->id]) }}" method="POST" enctype="multipart/form-data" id="formBayar">
                                @csrf
                                    <button type="submit" class="btn btn-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M12 5l0 14" />
                                            <path d="M5 12l14 0" />
                                        </svg>
                                        Bayar
                                    </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    document.getElementById('metode_pembayaran').addEventListener('change', function() {
        var qrisContainer = document.getElementById('qris-barcode-container');
        if (this.value === 'qris') {
            qrisContainer.style.display = 'block'; // Tampilkan gambar QRIS
        } else {
            qrisContainer.style.display = 'none'; // Sembunyikan gambar QRIS
        }
    });
</script>
