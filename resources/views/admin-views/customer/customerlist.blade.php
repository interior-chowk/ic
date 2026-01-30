@extends('layouts.back-end.app')
@section('title', 'Customer Contact List')

@push('css_or_js')
    <link href="{{ asset('public/assets/back-end/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <style>
        .square-btn,
        .btn.btn-sm.edit,
        .btn.btn-sm.delete {
            width: 52px;
            height: unset !important;
        }
    </style>
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                <img width="20" src="{{ asset('/public/assets/back-end/img/message.png') }}" alt="">
                Customer Messages
            </h2>
        </div>

        <div class="row mt-20">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row justify-content-between align-items-center flex-grow-1">
                            <div class="col-sm-4 col-md-6 col-lg-8 mb-2 mb-sm-0">
                                <h5>
                                    Customer Messages Table
                                    <span class="badge badge-soft-dark radius-50 fz-12">{{ $contacts->total() }}</span>
                                </h5>
                            </div>
                            <div class="col-sm-8 col-md-6 col-lg-4">
                                <form action="{{ url()->current() }}" method="GET">
                                    <div class="input-group input-group-merge input-group-custom">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="tio-search"></i>
                                            </div>
                                        </div>
                                        <input type="search" name="search" class="form-control"
                                            placeholder="Search by Name, Mobile, or Email" value="{{ $search }}">
                                        <button type="submit" class="btn btn--primary">Search</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table
                            class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                            <thead class="thead-light text-capitalize">
                                <tr>
                                    <th>SL</th>
                                    <th>Customer Name</th>
                                    <th>Contact Info</th>
                                    <th>Subject</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($contacts as $k => $contact)
                                    <tr style="background: {{ $contact->seen == 0 ? 'rgba(215,214,214,0.56)' : 'white' }}">
                                        <td>{{ $contacts->firstItem() + $k }}</td>
                                        <td>{{ $contact->name }}</td>
                                        <td>
                                            <div>{{ $contact->mobile_number }}</div>
                                            <div>{{ $contact->email }}</div>
                                        </td>
                                        <td>{{ $contact->subject }}</td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <a class="btn btn-outline-info btn-sm"
                                                    href="{{ route('admin.customer.customersview', $contact->id) }}">
                                                    View
                                                </a>

                                                <form action="{{ route('admin.customer.customersdelete', $contact->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    <button type="submit">Delete</button>
                                                </form>

                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 px-4 d-flex justify-content-lg-end">
                        {{ $contacts->links() }}
                    </div>

                    @if ($contacts->count() == 0)
                        <div class="text-center p-4">
                            <img class="mb-3 w-160" src="{{ asset('public/assets/back-end/svg/illustrations/sorry.svg') }}"
                                alt="No data">
                            <p>No data to show</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('.delete-confirm');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();

                    const form = this.closest('form');

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "This action cannot be undone!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit(); // normal Laravel form submission
                        }
                    });
                });
            });
        });
    </script>
@endpush
