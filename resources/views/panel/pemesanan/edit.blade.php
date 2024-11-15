<form action="{{ route('pemesanan.update', ['pemesanan' => $pemesanan->id]) }}" method="POST"
    enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="modal modal-blur fade" id="modal-editPesanan" tabindex="-1" role="dialog" aria-hidden="true"
        data-bs-backdrop="static">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $ingfo_sakkarepmu }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Nama pemesanan</label>
                                <select id="select-username" class="form-select @error('user_id') is-invalid @enderror" name="user_id">
                                    <option value="" disabled selected>Pilih User</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            data-custom-properties="<span class='avatar avatar-xs' style='background-image: url({{ $user->gambar_profile ? asset('storage/' . $user->gambar_profile) : '' }})'></span>"
                                            {{ $pemesanan->user_id == $user->id ? 'selected' : '' }}>
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
                                <label class="form-label">Kode pemesanan</label>
                                <input type="text" class="form-control @error('kode_pesanan') is-invalid @enderror"
                                    name="kode_pesanan" id="kode_pesanan"
                                    value="{{ old('kode_pesanan', $pemesanan->kode_pesanan) }}" maxlength="11" required>
                                @error('kode_pesanan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Tanggal</label>
                                <input type="date" class="form-control @error('tanggal') is-invalid @enderror"
                                    id="tanggal" name="tanggal" value="{{ old('tanggal', $pemesanan->tanggal) }}" required>
                                @error('tanggal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Status Pemesanan</label>
                                <select class="form-select @error('status') is-invalid @enderror" name="status" id="status">
                                    <option value="pending" {{ $pemesanan->status == 'pending' ? 'selected' : '' }}>
                                        Pending
                                    </option>
                                    <option value="selesai" {{ $pemesanan->status == 'selesai' ? 'selected' : '' }}>
                                        Selesai
                                    </option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#" class="btn btn-link link-secondary" data-bs-dismiss="modal">
                        Cancel
                    </a>
                    <button type="reset" class="btn">Reset</button>
                    <button type="submit" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 5l0 14" />
                            <path d="M5 12l14 0" />
                        </svg>
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var el;
        window.TomSelect && (new TomSelect(el = document.getElementById('select-username'), {
            copyClassesToDropdown: false,
            render: {
                item: function(data, escape) {
                    if (data.customProperties) {
                        return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
                    }
                    return '<div>' + escape(data.text) + '</div>';
                },
                option: function(data, escape) {
                    if (data.customProperties) {
                        return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
                    }
                    return '<div>' + escape(data.text) + '</div>';
                },
            },
        }));
    });
</script>
