<form id="delete-form-{{ $produk->id }}" action="{{ route('produk.destroy', ['produk' => $produk->id]) }}"
    method="POST">
    <div class="btn-list flex-nowrap">
        @csrf
        @method('delete')
        <a class="btn" onclick="confirmDelete({{ $produk->id }})">
            Hapus
        </a>
        <div class="dropdown">
            <button class="btn dropdown-toggle align-text-top" data-bs-toggle="dropdown">
                Action
            </button>
            <div class="dropdown-menu dropdown-menu-end">
                <a class="dropdown-item" href="{{ route('produk.show', ['produk' => $produk->id]) }}">
                    Detail
                </a>
                <!-- <a class="dropdown-item" href="{{ route('produk.edit', ['produk' => $produk->id]) }}" data-bs-toggle="modal"
                    data-bs-target="#modal-editData">
                    Edit
                </a> -->
            </div>
        </div>
    </div>
</form>
{{-- SweetAlert Library --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- Script untuk konfirmasi delete --}}
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'Anda yakin ingin menghapus?',
            text: "produk yang telah dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Jika pengguna mengonfirmasi, kirimkan formulir penghapusan
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }
</script>

{{-- Notifikasi setelah penghapusan --}}
@if ($message = Session::get('hapus'))
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });
        Toast.fire({
            icon: "success",
            title: '{{ $message }}'
        });
    </script>
@endif
