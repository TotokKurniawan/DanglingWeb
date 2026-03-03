@extends('admin.layout.app')
@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <h6>Daftar Customer / Buyer</h6>
                        <form method="GET" action="{{ route('admin.buyers.index') }}" class="d-flex">
                            <input type="text" name="search" class="form-control form-control-sm me-2" placeholder="Cari nama/email..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-sm btn-primary mb-0">Cari</button>
                        </form>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">No</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Email / Telepon</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Bergabung Sejak</th>
                                        <th class="text-secondary opacity-7 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($buyers as $index => $buyer)
                                        <tr>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0 px-3">{{ $buyers->firstItem() + $index }}</p>
                                            </td>
                                            <td>
                                                <div class="d-flex px-3 py-1">
                                                    <div>
                                                        @if($buyer->user->photo_path)
                                                            <img src="{{ url('storage/' . $buyer->user->photo_path) }}" class="avatar avatar-sm me-3" alt="{{ $buyer->user->name }}">
                                                        @else
                                                            <img src="../assets/img/team-2.jpg" class="avatar avatar-sm me-3" alt="Default Avatar">
                                                        @endif
                                                    </div>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $buyer->user->name ?? 'User Tidak Diketahui' }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">{{ $buyer->user->email ?? '-' }}</p>
                                                <p class="text-xs text-secondary mb-0">{{ $buyer->phone ?? '-' }}</p>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <p class="text-xs font-weight-bold mb-0">{{ $buyer->created_at->format('d M Y') }}</p>
                                            </td>
                                            <td class="align-middle text-center">
                                                <span class="badge bg-gradient-secondary">Lihat Order</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-sm text-secondary">Belum ada data buyer.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            
                            <div class="d-flex justify-content-center mt-4">
                                {{ $buyers->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
