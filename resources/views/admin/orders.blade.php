@extends('admin.layout.app')
@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <h6>Riwayat Semua Pesanan (Global)</h6>
                    </div>
                    
                    <div class="card-body pt-0 pb-2">
                        <form method="GET" action="{{ route('admin.orders.index') }}" class="row g-2 mb-3 mt-2">
                            <div class="col-md-3">
                                <select name="status" class="form-select form-select-sm">
                                    <option value="">Semua Status</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Accepted</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="seller_id" class="form-select form-select-sm">
                                    <option value="">Semua Seller</option>
                                    @foreach($sellers as $seller)
                                        <option value="{{ $seller->id }}" {{ request('seller_id') == $seller->id ? 'selected' : '' }}>
                                            {{ $seller->store_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-sm btn-primary w-100 mb-0">Filter</button>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-secondary w-100 mb-0">Reset</a>
                            </div>
                        </form>
                    </div>

                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">No / ID Order</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Customer</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Penjual</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Total</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Status</th>
                                        <th class="text-secondary opacity-7 text-center">Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($orders as $index => $order)
                                        <tr>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0 px-3">#{{ $order->id }}</p>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0 px-3">{{ $order->buyer->user->name ?? 'Unknown Buyer' }}</p>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0 px-3">{{ $order->seller->store_name ?? 'Unknown Seller' }}</p>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0 px-3">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                @php
                                                    $bagdes = [
                                                        'pending' => 'bg-gradient-warning',
                                                        'accepted' => 'bg-gradient-info',
                                                        'completed' => 'bg-gradient-success',
                                                        'cancelled' => 'bg-gradient-danger',
                                                        'rejected' => 'bg-gradient-danger',
                                                    ];
                                                    $badgeClass = $bagdes[strtolower($order->status)] ?? 'bg-gradient-secondary';
                                                @endphp
                                                <span class="badge badge-sm {{ $badgeClass }}">{{ ucfirst($order->status) }}</span>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <p class="text-xs font-weight-bold mb-0">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-sm text-secondary">Belum ada riwayat pesanan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            
                            <div class="d-flex justify-content-center mt-4">
                                {{ $orders->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
