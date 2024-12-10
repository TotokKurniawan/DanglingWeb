@extends('admin.layout.app')
@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <h6>Data Operator</h6>
                        <a href="{{ route('Tambahadmin') }}" class="btn btn-primary btn-sm" data-toggle="tooltip"
                            data-original-title="Tambah data">
                            <i class="fas fa-plus"></i> Tambah
                        </a>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">No
                                        </th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama
                                        </th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Role
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Password</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs opacity-7">Action</th>
                                    </tr>
                                </thead>
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
                                                    <div class="d-flex flex-column " style="margin-left: 20px;">
                                                        <h6 class="mb-0 text-sm">{{ $user->nama }}</h6>
                                                        <p class="text-xs text-secondary mb-0">{{ $user->email }}</p>
                                                    </div>
                                                </div>
                                            </td>

                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">{{ $user->password }}</p>
                                            </td>
                                            <td class="align-middle">
                                                <a href="#" class="text-secondary font-weight-bold text-xs"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editOperatorModal-{{ $user->id }}">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <span class="mx-2">|</span>

                                                <a href="#" class="text-danger font-weight-bold text-xs"
                                                    onclick="confirmDelete('{{ $user->id }}')">
                                                    <i class="fas fa-trash"></i> Hapus
                                                </a>

                                                <form id="delete-form-{{ $user->id }}"
                                                    action="{{ route('user.destroy', $user->id) }}" method="POST"
                                                    style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>

                                            </td>
                                        </tr>
                                        @include('admin.modal.modal-operator')
                                    @endforeach
                                </tbody>
                            </table>

                            <div class="d-flex justify-content-center mt-4 text-black">
                                <ul class="pagination">
                                    {{-- Previous Page Link --}}
                                    <li class="page-item {{ $users->onFirstPage() ? 'disabled' : '' }}">
                                        <a class="page-link" href="{{ $users->previousPageUrl() }}" aria-label="Previous">
                                            <span aria-hidden="true">&laquo;</span>
                                        </a>
                                    </li>

                                    {{-- Pagination Elements --}}
                                    @foreach ($users->links()->elements[0] as $page => $url)
                                        <li class="page-item {{ $page == $users->currentPage() ? 'active' : '' }}">
                                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                        </li>
                                    @endforeach

                                    {{-- Next Page Link --}}
                                    <li class="page-item {{ $users->hasMorePages() ? '' : 'disabled' }}">
                                        <a class="page-link" href="{{ $users->nextPageUrl() }}" aria-label="Next">
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
    </div>

    @include('admin.layout.footer')
    </div>
@endsection
