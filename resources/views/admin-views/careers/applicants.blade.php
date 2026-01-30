@extends('layouts.back-end.app')
@section('title', \App\CPU\translate('Applicant List'))

@push('css_or_js')
    <link href="{{ asset('public/assets/back-end/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                <img src="{{ asset('/public/assets/back-end/img/employee.png') }}" width="20" alt="">
                {{ \App\CPU\translate('applicant_list') }}
            </h2>
        </div>

        <!-- Content -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header flex-wrap gap-10">
                        <h5 class="mb-0 d-flex gap-2 align-items-center">
                            {{ \App\CPU\translate('applicant_table') }}
                            <span class="badge badge-soft-dark radius-50 fz-12">{{ $applicants->total() }}</span>
                        </h5>

                        <!-- Search -->
                        <form action="{{ url()->current() }}" method="GET">
                            <div class="input-group input-group-merge input-group-custom">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><i class="tio-search"></i></div>
                                </div>
                                <input type="search" name="search" class="form-control"
                                    placeholder="{{ \App\CPU\translate('search_by_name_or_email') }}"
                                    value="{{ request('search') }}" required>
                                <button type="submit" class="btn btn--primary">{{ \App\CPU\translate('search') }}</button>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table id="datatable"
                            class="table table-hover table-borderless table-thead-bordered table-align-middle card-table w-100">
                            <thead class="thead-light thead-50 text-capitalize table-nowrap">
                                <tr>
                                    <th>{{ \App\CPU\translate('SL') }}</th>
                                    <th>{{ \App\CPU\translate('Name') }}</th>
                                    <th>{{ \App\CPU\translate('Email') }}</th>
                                    <th>{{ \App\CPU\translate('Phone') }}</th>
                                    <th>{{ \App\CPU\translate('Job Title') }}</th>
                                    <th>{{ \App\CPU\translate('Resume') }}</th>
                                    <th>{{ \App\CPU\translate('Applied On') }}</th>
                                    <th class="text-center">{{ \App\CPU\translate('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($applicants as $k => $applicant)
                                    <tr>
                                        <td>{{ $k + 1 }}</td>
                                        <td class="text-capitalize">{{ $applicant->full_name }}</td>
                                        <td>{{ $applicant->email }}</td>
                                        <td>{{ $applicant->phone }}</td>
                                        <td>{{ $applicant->career->title ?? '-' }}</td>
                                        <td>
                                            @if ($applicant->resume)
                                                <a href="{{ asset('storage/app/public/' . $applicant->resume) }}"
                                                    target="_blank" class="btn btn-sm btn-outline-info">
                                                    {{ \App\CPU\translate('View') }}
                                                </a>
                                            @else
                                                {{ \App\CPU\translate('No Resume') }}
                                            @endif
                                        </td>
                                        <td>{{ $applicant->created_at->diffForHumans() }}</td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center">
                                                <a class="btn btn-outline-danger btn-sm delete square-btn"
                                                    title="{{ \App\CPU\translate('Delete') }}"
                                                    data-id="{{ $applicant->id }}">
                                                    <i class="tio-delete"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                @if ($applicants->isEmpty())
                                    <tr>
                                        <td colspan="8" class="text-center">{{ \App\CPU\translate('No data found') }}
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="table-responsive mt-4">
                        <div class="px-4 d-flex justify-content-lg-end">
                            {{ $applicants->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).on('click', '.delete', function() {
            var id = $(this).data("id");

            Swal.fire({
                title: '{{ \App\CPU\translate('Are_you_sure_delete') }}?',
                text: "{{ \App\CPU\translate('You_will_not_be_able_to_revert_this') }}!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '{{ \App\CPU\translate('Yes') }}, {{ \App\CPU\translate('delete_it') }}!',
                cancelButtonText: "{{ \App\CPU\translate('cancel') }}",
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{ route('admin.employee.applicant.delete') }}",
                        method: 'POST',
                        data: {
                            id: id
                        },
                        success: function() {
                            toastr.success('{{ \App\CPU\translate('deleted_successfully') }}');
                            location.reload();
                        }
                    });
                }
            });
        });
    </script>
@endpush
