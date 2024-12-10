@extends('admin.layout.app')
@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Total Pedagang</p>
                                    <h5 class="font-weight-bolder">
                                        {{ $totalPedagang }}
                                    </h5>
                                    <p class="mb-0">
                                        <span
                                            class="text-success text-sm font-weight-bolder">{{ $pedagangPercentageText }}</span>
                                        since last Year
                                    </p>

                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle">
                                    <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Total Pelanggan</p>
                                    <h5 class="font-weight-bolder">
                                        {{ $totalPembeli }}
                                        <p class="mb-0">
                                            <span
                                                class="text-success text-sm font-weight-bolder">{{ $pembeliPercentageText }}</span>
                                            since last Year
                                        </p>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-danger shadow-danger text-center rounded-circle">
                                    <i class="ni ni-world text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-sm-6">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Total Operator</p>
                                    <h5 class="font-weight-bolder">
                                        {{ $totalOperator }}
                                        <p class="mb-0">
                                            <span
                                                class="text-success text-sm font-weight-bolder">{{ $operatorPercentageText }}</span>
                                            since last Year
                                        </p>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-warning shadow-warning text-center rounded-circle">
                                    <i class="ni ni-cart text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="row">
                <!-- Card untuk Grafik Pedagang -->
                <div class="col-lg-6 mb-lg-0 mb-4">
                    <div class="card z-index-2 h-100">
                        <div class="card-header pb-0 pt-3 bg-transparent d-flex justify-content-between align-items-center">
                            <h6 class="text-capitalize mb-0">Pedagang Overview</h6>
                            <div>
                                <button id="changePedagangChartType" class="btn btn-sm btn-primary">Ganti Tipe
                                    Chart</button>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <div class="chart">
                                <canvas id="grafik-pedagang" class="chart-canvas" height="450"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card untuk Grafik Pembeli -->
                <div class="col-lg-6 mb-lg-0 mb-4">
                    <div class="card z-index-2 h-100">
                        <div class="card-header pb-0 pt-3 bg-transparent d-flex justify-content-between align-items-center">
                            <h6 class="text-capitalize mb-0">Pembeli Overview</h6>
                            <div>
                                <button id="changePembeliChartType" class="btn btn-sm btn-primary">Ganti Tipe Chart</button>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <div class="chart">
                                <canvas id="grafik-pembeli" class="chart-canvas" height="450"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var months = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September",
                        "Oktober", "November", "Desember"
                    ];
                    var pedagangTotals = @json($pedagangTotals);
                    var pembeliTotals = @json($pembeliTotals);

                    // Tipe awal untuk chart Pedagang dan Pembeli
                    var pedagangChartType = 'line';
                    var pembeliChartType = 'line';

                    // Fungsi membuat chart Pedagang
                    function createPedagangChart(type) {
                        var ctx = document.getElementById('grafik-pedagang').getContext('2d');
                        return new Chart(ctx, {
                            type: type,
                            data: {
                                labels: months,
                                datasets: [{
                                    label: 'Jumlah Pedagang',
                                    data: pedagangTotals,
                                    borderColor: 'rgba(75, 192, 192, 1)',
                                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                    fill: true,
                                    tension: 0.4
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        display: true,
                                        position: 'top'
                                    },
                                    tooltip: {
                                        mode: 'index',
                                        intersect: false
                                    }
                                },
                                scales: {

                                    y: {
                                        title: {
                                            display: true,
                                            text: 'Jumlah Pedagang'
                                        }
                                    }
                                }
                            }
                        });
                    }

                    // Fungsi membuat chart Pembeli
                    function createPembeliChart(type) {
                        var ctx = document.getElementById('grafik-pembeli').getContext('2d');
                        return new Chart(ctx, {
                            type: type,
                            data: {
                                labels: months,
                                datasets: [{
                                    label: 'Jumlah Pembeli',
                                    data: pembeliTotals,
                                    borderColor: 'rgba(153, 102, 255, 1)',
                                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                                    fill: true,
                                    tension: 0.4
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        display: true,
                                        position: 'top'
                                    },
                                    tooltip: {
                                        mode: 'index',
                                        intersect: false
                                    }
                                },
                                scales: {
                                    y: {
                                        title: {
                                            display: true,
                                            text: 'Jumlah Pembeli'
                                        }
                                    }
                                }
                            }
                        });
                    }

                    // Initialize charts
                    var pedagangChart = createPedagangChart(pedagangChartType);
                    var pembeliChart = createPembeliChart(pembeliChartType);

                    // Button untuk toggle chart Pedagang
                    document.getElementById('changePedagangChartType').addEventListener('click', function() {
                        pedagangChart.destroy();
                        pedagangChartType = pedagangChartType === 'line' ? 'bar' : 'line';
                        pedagangChart = createPedagangChart(pedagangChartType);
                    });

                    // Button untuk toggle chart Pembeli
                    document.getElementById('changePembeliChartType').addEventListener('click', function() {
                        pembeliChart.destroy();
                        pembeliChartType = pembeliChartType === 'line' ? 'bar' : 'line';
                        pembeliChart = createPembeliChart(pembeliChartType);
                    });
                });
            </script>

        </div>
    </div>
    <div class="row mt-4">
        <div class="col-lg-12 mb-lg-0 mb-4">
            <div class="card ">
                <div class="card-header pb-0 p-3">
                    <div class="d-flex justify-content-between">
                        <h6 class="mb-2">Data Operator</h6>
                    </div>
                </div>
                <div class="table-responsive ">
                    <table class="table align-items-center ">
                        <tbody>
                            @foreach ($users as $index => $user)
                                <tr class="text-center">
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $index + 1 }}</p>
                                    </td>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div>
                                                <!-- Set ukuran dengan max-width dan max-height -->
                                                <img src="{{ asset('storage/foto_mitra/' . basename($user->foto)) }}"
                                                    alt="Foto Mitra"
                                                    style="max-width: 80px; max-height: 80px; object-fit: cover; ">
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $user->nama }}</h6>
                                                <p class="text-xs text-secondary mb-0">{{ $user->email }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $user->role }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $user->password }}</p>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @include('admin.layout.footer')
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Login Berhasil',
                text: '{{ session('success') }}',
                confirmButtonText: 'OK'
            });
        </script>
    @endif
@endsection
