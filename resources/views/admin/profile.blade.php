@extends('admin.layout.app')
@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header pb-0">
                        <div class="d-flex align-items-center">
                            <p class="mb-0">User Information</p>
                            <button type="submit" class="btn btn-primary btn-sm ms-auto"
                                form="edit-profile-form">Update</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="edit-profile-form" action="{{ route('profile.update', $user->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf <!-- Laravel CSRF token -->
                            @method('PUT') <!-- Menggunakan metode PUT untuk update -->

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="first-name" class="form-control-label">Full name</label>
                                        <input id="nama" name="nama" class="form-control" type="text">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="email" class="form-control-label">Email address</label>
                                        <input id="email" name="email" class="form-control" type="email"
                                            value="{{ old('email', $user->email) }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="password" class="form-control-label">Password</label>
                                        <input id="password" name="password" class="form-control" type="text"
                                            placeholder="Leave blank if you don't want to change">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="foto" class="form-control-label">Foto</label>
                                        <input id="foto" name="foto" class="form-control" type="file">
                                        <small class="form-text text-muted">Format file: jpg, png, maksimal 2MB.</small>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>



            <div class="col-md-4">
                <div class="card card-profile">
                    <img src="{{ asset('admin_assets/foto_user/alun-alun-kota-batu-HD.webp') }}" alt="Image placeholder"
                        class="card-img-top">

                    <div class="row justify-content-center">
                        <div class="col-4 col-lg-4 order-lg-2">
                            <div class="mt-n4 mt-lg-n6 mb-4 mb-lg-0 text-center">
                                <a href="javascript:;">
                                    <img src="{{ Storage::url($user->foto) }}"
                                        class="rounded-circle img-fluid border border-9 border-white" alt="User Foto">
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body pt-0">
                        <div class="text-center mt-4">
                            <h5>{{ $user->nama }}<span class="font-weight-light"></span></h5> <!-- Menampilkan nama -->
                            <div class="h6 font-weight-300">
                                <i class="ni location_pin mr-2"></i>{{ $user->email }} <!-- Menampilkan email -->
                            </div>
                            <div class="h6 mt-4">
                                <i class="ni business_briefcase-24 mr-2"></i>{{ $user->role }} <!-- Menampilkan role -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        @include('admin.layout.footer')
    </div>
@endsection
