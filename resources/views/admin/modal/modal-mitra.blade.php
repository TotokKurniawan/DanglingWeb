<!-- Modal untuk Edit Mitra -->
@foreach ($mitras as $mitra)
    <div class="modal fade" id="editMitraModal-{{ $mitra->id }}" tabindex="-1"
        aria-labelledby="editMitraModalLabel-{{ $mitra->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editMitraModalLabel-{{ $mitra->id }}">Edit Mitra</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            <form id="edit-mitra-form-{{ $mitra->id }}" action="{{ route('partners.update', $mitra->id) }}"
                    method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" class="form-control" name="id" value="{{ $mitra->id }}">
                        <div class="mb-1">
                            <label for="nama" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="edit-nama-{{ $mitra->id }}" name="nama"
                                value="{{ $mitra->nama }}" required>
                        </div>
                        <div class="mb-6">
                            <label for="perusahaan" class="form-label">Perusahaan</label>
                            <input type="text" class="form-control" id="edit-perusahaan-{{ $mitra->id }}"
                                name="perusahaan" value="{{ $mitra->perusahaan }}" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
