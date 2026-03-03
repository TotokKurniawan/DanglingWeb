@extends('admin.layout.app')
@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <h6>Pengaturan Sistem</h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.settings.update') }}">
                            @csrf
                            @method('PUT')

                            @forelse ($settings as $group => $items)
                                <h6 class="text-uppercase text-secondary text-xs font-weight-bolder mt-3 mb-2">
                                    {{ ucfirst($group) }}
                                </h6>
                                <div class="table-responsive">
                                    <table class="table align-items-center mb-3">
                                        <thead>
                                            <tr>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                                    style="width: 30%">Key</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                                    style="width: 35%">Value</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                                    style="width: 35%">Deskripsi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($items as $setting)
                                                <tr>
                                                    <td>
                                                        <span class="text-sm font-weight-bold">{{ $setting->label ?? $setting->key }}</span>
                                                        <br>
                                                        <code class="text-xs">{{ $setting->key }}</code>
                                                    </td>
                                                    <td>
                                                        @if ($setting->type === 'boolean')
                                                            <select name="settings[{{ $setting->id }}]"
                                                                class="form-select form-select-sm" style="width:auto;">
                                                                <option value="1"
                                                                    {{ $setting->value == '1' ? 'selected' : '' }}>Ya
                                                                </option>
                                                                <option value="0"
                                                                    {{ $setting->value == '0' ? 'selected' : '' }}>Tidak
                                                                </option>
                                                            </select>
                                                        @else
                                                            <input type="text" name="settings[{{ $setting->id }}]"
                                                                value="{{ $setting->value }}"
                                                                class="form-control form-control-sm">
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="text-xs text-secondary">{{ $setting->description ?? '-' }}</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @empty
                                <p class="text-sm text-secondary">Belum ada pengaturan.</p>
                            @endforelse

                            <button type="submit" class="btn btn-primary btn-sm">Simpan Pengaturan</button>
                        </form>
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
