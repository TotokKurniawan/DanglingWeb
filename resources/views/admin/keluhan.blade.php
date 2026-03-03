@extends('admin.layout.app')
@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <h6>Table Keluhan</h6>
                        <a href="{{ route('admin.complaints.export') }}" class="btn bg-gradient-success btn-sm mb-0">
                            <i class="fas fa-file-excel me-2"></i> Export CSV
                        </a>
                    </div>

                    {{-- Filter --}}
                    <div class="card-body pt-0 pb-2">
                        <form method="GET" action="{{ route('admin.complaints.index') }}" class="row g-2 mb-3">
                            <div class="col-md-3">
                                <select name="seller_id" class="form-select form-select-sm">
                                    <option value="">Semua Seller</option>
                                    @foreach ($sellers as $seller)
                                        <option value="{{ $seller->id }}"
                                            {{ request('seller_id') == $seller->id ? 'selected' : '' }}>
                                            {{ $seller->store_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="status" class="form-select form-select-sm">
                                    <option value="">Semua Status</option>
                                    <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>
                                        In Progress</option>
                                    <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>
                                        Resolved</option>
                                    <option value="dismissed" {{ request('status') == 'dismissed' ? 'selected' : '' }}>
                                        Dismissed</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="date_from" class="form-control form-control-sm"
                                    value="{{ request('date_from') }}" placeholder="Dari tanggal">
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="date_to" class="form-control form-control-sm"
                                    value="{{ request('date_to') }}" placeholder="Sampai tanggal">
                            </div>
                            <div class="col-md-3 d-flex gap-1">
                                <button type="submit" class="btn btn-sm btn-primary mb-0">Filter</button>
                                <a href="{{ route('admin.complaints.index') }}" class="btn btn-sm btn-outline-secondary mb-0">Reset</a>
                            </div>
                        </form>

                        {{-- Tabel --}}
                        <div class="table-responsive p-0">
                            <table class="table align-items-center justify-content-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">No</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Pembeli</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Pedagang</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Order</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Deskripsi</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center opacity-7 ps-2">Rating</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center opacity-7 ps-2">Status</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center opacity-7 ps-2">Tanggal</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center opacity-7 ps-2">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($keluhans as $keluhan)
                                        <tr>
                                            <td class="text-sm font-weight-bold mb-0 ps-3">
                                                {{ ($keluhans->currentPage() - 1) * $keluhans->perPage() + $loop->iteration }}
                                            </td>
                                            <td class="text-sm mb-0">{{ $keluhan->buyer->name ?? 'N/A' }}</td>
                                            <td class="text-sm mb-0">{{ $keluhan->seller->store_name ?? 'N/A' }}</td>
                                            <td class="text-sm mb-0">#{{ $keluhan->order_id ?? '-' }}</td>
                                            <td class="text-sm mb-0" style="max-width:200px; white-space:normal;">
                                                {{ Str::limit($keluhan->description, 80) }}
                                            </td>
                                            <td class="align-middle text-center text-sm font-weight-bold">
                                                {{ $keluhan->rating }}/5
                                            </td>
                                            <td class="align-middle text-center">
                                                @php
                                                    $badge = match($keluhan->status) {
                                                        'open' => 'bg-gradient-warning',
                                                        'in_progress' => 'bg-gradient-info',
                                                        'resolved' => 'bg-gradient-success',
                                                        'dismissed' => 'bg-gradient-secondary',
                                                        default => 'bg-gradient-dark',
                                                    };
                                                @endphp
                                                <span class="badge badge-sm {{ $badge }}">
                                                    {{ ucfirst(str_replace('_', ' ', $keluhan->status)) }}
                                                </span>
                                            </td>
                                            <td class="align-middle text-center text-xs">
                                                {{ $keluhan->created_at->format('d/m/Y') }}
                                            </td>
                                            <td class="align-middle text-center">
                                                <form method="POST"
                                                    action="{{ route('admin.complaints.status', $keluhan->id) }}"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <select name="status" class="form-select form-select-sm d-inline-block"
                                                        style="width:auto; font-size:0.75rem;"
                                                        onchange="this.form.submit()">
                                                        <option value="open" {{ $keluhan->status == 'open' ? 'selected' : '' }}>Open</option>
                                                        <option value="in_progress" {{ $keluhan->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                        <option value="resolved" {{ $keluhan->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                                        <option value="dismissed" {{ $keluhan->status == 'dismissed' ? 'selected' : '' }}>Dismissed</option>
                                                    </select>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center text-sm py-3">Tidak ada data keluhan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>

                            {{-- Pagination --}}
                            <div class="d-flex justify-content-center mt-4 text-black">
                                {{ $keluhans->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('admin.layout.footer')
    </div>

    @if (session('success'))
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '{{ session('success') }}',
                confirmButtonText: 'OK'
            });
        </script>
    @endif
@endsection
