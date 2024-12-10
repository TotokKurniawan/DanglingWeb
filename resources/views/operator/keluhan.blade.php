@extends('operator.layout.app')
@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6> Table Keluhan </h6>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center justify-content-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-sm font-weight-bolder opacity-7">No
                                        </th>
                                        <th class="text-uppercase text-secondary text-sm font-weight-bolder opacity-7">Nama
                                        </th>
                                        <th class="text-uppercase text-secondary text-sm font-weight-bolder opacity-7 ps-2">
                                            Pedagang</th>
                                        <th class="text-uppercase text-secondary text-sm font-weight-bolder opacity-7 ps-2">
                                            Deskripsi</th>
                                        <th
                                            class="text-uppercase text-secondary text-sm font-weight-bolder text-center opacity-7 ps-2">
                                            Rating</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($keluhans as $keluhan)
                                        <tr>
                                            <td class="text-sm font-weight-bold mb-0">{{ $loop->iteration }}</td>
                                            <td class="text-sm font-weight-bold mb-0">
                                                {{ $keluhan->pembeli->nama ?? 'N/A' }} <!-- Nama pembeli -->
                                            </td>
                                            <td class="text-sm font-weight-bold mb-0">
                                                {{ $keluhan->pedagang->namaToko ?? 'N/A' }} <!-- Nama pedagang -->
                                            </td>
                                            <td class="text-sm mb-0">{{ $keluhan->deskripsi }}</td>
                                            <td class="align-middle text-center text-sm font-weight-bold">
                                                {{ $keluhan->rating }}
                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>


                            <!-- Pagination -->
                            <div class="d-flex justify-content-center mt-4 text-black">
                                <ul class="pagination">
                                    {{-- Previous Page Link --}}
                                    <li class="page-item {{ $keluhans->onFirstPage() ? 'disabled' : '' }}">
                                        <a class="page-link" href="{{ $keluhans->previousPageUrl() }}"
                                            aria-label="Previous">
                                            <span aria-hidden="true">&laquo;</span>
                                        </a>
                                    </li>

                                    {{-- Pagination Elements --}}
                                    @foreach ($keluhans->links()->elements[0] as $page => $url)
                                        <li class="page-item {{ $page == $keluhans->currentPage() ? 'active' : '' }}">
                                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                        </li>
                                    @endforeach

                                    {{-- Next Page Link --}}
                                    <li class="page-item {{ $keluhans->hasMorePages() ? '' : 'disabled' }}">
                                        <a class="page-link" href="{{ $keluhans->nextPageUrl() }}" aria-label="Next">
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
@endsection
