<!-- Modal untuk Edit Mitra -->
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<div class="modal fade" id="editOperatorModal-{{ $user->id }}" tabindex="-1"
    aria-labelledby="editOperatorModalLabel-{{ $user->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editOperatorModalLabel-{{ $user->id }}">Edit Mitra</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="edit-mitra-form-{{ $user->id }}" action="{{ route('operator.update', $user->id) }}"
                method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" name="id" value="{{ $user->id }}">
                    <div class="mb-3">
                        <label for="edit-email-{{ $user->id }}" class="form-label">Email</label>
                        <input type="email" class="form-control" id="edit-email-{{ $user->id }}" name="email"
                            value="{{ $user->email }}" placeholder="Masukkan email" required readonly>
                    </div>
                    <div class="mb-3">
                        <label for="edit-nama-{{ $user->id }}" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="edit-nama-{{ $user->id }}" name="nama"
                            value="{{ $user->nama }}" placeholder="Masukkan nama lengkap" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit-password-{{ $user->id }}" class="form-label">Password</label>
                        <input type="password" class="form-control" id="edit-password-{{ $user->id }}"
                            name="password" placeholder="Masukkan password">
                    </div>

                    <div class="mb-3">
                        <label for="edit-role-{{ $user->id }}" class="form-label">Role</label>
                        <select class="form-control" id="edit-role-{{ $user->id }}" name="role" required>
                            <option value="" disabled>Pilih Role</option>
                            <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="operator" {{ $user->role == 'operator' ? 'selected' : '' }}>Operator
                            </option>
                            <!-- Tambahkan opsi lainnya sesuai kebutuhan -->
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
