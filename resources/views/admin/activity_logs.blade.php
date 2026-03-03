@extends('admin.layout.app')
@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <h6>Catatan Aktivitas Sistem (Activity Logs)</h6>
                        <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="d-flex">
                            <input type="text" name="event" class="form-control form-control-sm me-2" placeholder="Cari event..." value="{{ request('event') }}">
                            <button type="submit" class="btn btn-sm btn-primary mb-0">Cari</button>
                        </form>
                    </div>
                    
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0 mt-3">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Waktu</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Event</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Subjek Tracker</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Validator / User</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">IP Address</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($logs as $log)
                                        <tr>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0 px-3">{{ $log->created_at->format('d/m/Y H:i:s') }}</p>
                                            </td>
                                            <td>
                                                <span class="badge bg-gradient-info">{{ $log->event }}</span>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0 px-3">{{ class_basename($log->subject_type) }} #{{ $log->subject_id ?? '-' }}</p>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0 px-3">{{ $log->user->name ?? 'System/Guest' }}</p>
                                            </td>
                                            <td>
                                                <p class="text-xs text-secondary mb-0 px-3">{{ $log->ip_address ?? '-' }}</p>
                                            </td>
                                            <td class="align-middle text-center">
                                                <button type="button" class="btn btn-link text-secondary mb-0 p-0" data-bs-toggle="modal" data-bs-target="#detailModal{{ $log->id }}">
                                                    <i class="fa fa-eye text-xs"></i> Lihat Data
                                                </button>

                                                <!-- Modal Properties Detail -->
                                                <div class="modal fade" id="detailModal{{ $log->id }}" tabindex="-1" aria-labelledby="detailModalLabel{{ $log->id }}" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content text-start">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="detailModalLabel{{ $log->id }}">Detail Properties Log</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <pre class="bg-light p-3 rounded text-sm text-dark" style="max-height: 400px; overflow-y: auto;">{{ json_encode($log->properties, JSON_PRETTY_PRINT) }}</pre>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Tutup</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-sm text-secondary">Belum ada riwayat aktivitas.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            
                            <div class="d-flex justify-content-center mt-4">
                                {{ $logs->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
