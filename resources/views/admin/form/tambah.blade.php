@extends('admin.layout.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <h6>Tambah Mitra</h6>
                    </div>
                    <div class="card-body px-4 pt-4 pb-2">
                        <form action="{{ route('partners.store') }}" method="POST">
                            @csrf <!-- Tambahkan CSRF protection jika belum ada -->

                            <div class="row mb-2">
                                <!-- Nama field -->
                                <div class="col-md-12"> <!-- Change col-md-6 to col-md-12 to span the full row -->
                                    <div class="form-group">
                                        <label for="name" class="form-label">Nama</label>
                                        <input type="text" class="form-control" id="name" name="name"
                                            placeholder="Masukkan nama" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <!-- Email field -->
                                <div class="col-md-12"> <!-- Change col-md-6 to col-md-12 to span the full row -->
                                    <div class="form-group">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            placeholder="Masukkan email" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <!-- Perusahaan field -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="company" class="form-label">Perusahaan</label>
                                        <input type="text" class="form-control" id="company" name="company"
                                            placeholder="Masukkan perusahaan" required>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @include('admin.layout.footer')
    </div>
@endsection
