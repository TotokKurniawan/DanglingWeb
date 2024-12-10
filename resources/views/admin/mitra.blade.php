@extends('admin.layout.app')
@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <h6>Table Mitra</h6>
                        <a href="{{ route('Tambah-Pesanan') }}" class="btn btn-primary btn-sm" data-toggle="tooltip"
                            data-original-title="Tambah data">
                            <i class="fas fa-plus"></i> Tambah
                        </a>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            No</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Nama</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Perusahaan</th>
                                        <th class="text-secondary opacity-7">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($mitras as $mitra)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm ">
                                                            {{ $loop->iteration + ($mitras->currentPage() - 1) * $mitras->perPage() }}
                                                        </h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $mitra->nama }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <span class="text-xs">{{ $mitra->Perusahaan }}</span>
                                            </td>
                                            <td class="align-middle text-center">
                                                <!-- Tombol Edit -->
                                                <!-- Tombol Edit -->
                                                <a href="#" class="text-secondary font-weight-bold text-xs"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editMitraModal-{{ $mitra->id }}">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>

                                                <span class="mx-2">|</span>

                                                <!-- Tombol Hapus -->
                                                <a href="#" class="text-danger font-weight-bold text-xs"
                                                    onclick="confirmDelete('{{ $mitra->id }}')">
                                                    <i class="fas fa-trash"></i> Hapus
                                                </a>

                                                <!-- Formulir untuk menghapus -->
                                                <form id="delete-form-{{ $mitra->id }}"
                                                    action="{{ route('mitras.destroy', $mitra->id) }}" method="POST"
                                                    style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <!-- Pagination -->
                            <div class="d-flex justify-content-center mt-4 text-black">
                                <ul class="pagination">
                                    {{-- Previous Page Link --}}
                                    <li class="page-item {{ $mitras->onFirstPage() ? 'disabled' : '' }}">
                                        <a class="page-link" href="{{ $mitras->previousPageUrl() }}" aria-label="Previous">
                                            <span aria-hidden="true">&laquo;</span>
                                        </a>
                                    </li>

                                    {{-- Pagination Elements --}}
                                    @foreach ($mitras->links()->elements[0] as $page => $url)
                                        <li class="page-item {{ $page == $mitras->currentPage() ? 'active' : '' }}">
                                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                        </li>
                                    @endforeach

                                    {{-- Next Page Link --}}
                                    <li class="page-item {{ $mitras->hasMorePages() ? '' : 'disabled' }}">
                                        <a class="page-link" href="{{ $mitras->nextPageUrl() }}" aria-label="Next">
                                            <span aria-hidden="true">&raquo;</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('admin.layout.footer')
    </div>
    @include('admin.modal.modal-mitra')
@endsection
