@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('Bulk Purchase List'))

@push('css_or_js')
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex gap-2">
                <img width="20" src="{{ asset('/public/assets/back-end/img/brand.png') }}" alt="">
                {{ \App\CPU\translate('Bulk') }} {{ \App\CPU\translate('Purchase') }} {{ \App\CPU\translate('List') }}
                <span class="badge badge-soft-dark radius-50 fz-14">{{ $br->total() }}</span>
            </h2>
        </div>
        <!-- End Page Title -->

        <div class="row mt-20">
            <div class="col-md-12">
                <div class="card">
                    <!-- Data Table Top -->
                    <div class="px-3 py-4">
                        <div class="row g-2 flex-grow-1">
                            <div class="col-sm-8 col-md-6 col-lg-4">
                                <!-- Search -->
                                <form action="{{ url()->current() }}" method="GET">
                                    <div class="input-group input-group-custom input-group-merge">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="tio-search"></i>
                                            </div>
                                        </div>
                                        <input type="search" name="search" class="form-control"
                                            placeholder="{{ \App\CPU\translate('Search_by_Name') }}"
                                            aria-label="Search by ID or name" value="{{ $search ?? '' }}">
                                        <button type="submit"
                                            class="btn btn--primary input-group-text">{{ \App\CPU\translate('Search') }}</button>
                                    </div>
                                </form>
                                <!-- End Search -->
                            </div>
                        </div>
                        <!-- End Row -->
                    </div>
                    <!-- End Data Table Top -->

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table style="text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};"
                                class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                                <thead class="thead-light thead-50 text-capitalize">
                                    <tr>
                                        <th>{{ \App\CPU\translate('SL') }}</th>
                                        <th>{{ \App\CPU\translate('User') }}</th>
                                        <th>{{ \App\CPU\translate('Product') }}</th>
                                        <th>{{ \App\CPU\translate('Quantity') }}</th>
                                        <th>{{ \App\CPU\translate('Remark') }}</th>
                                        <th>{{ \App\CPU\translate('Created At') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($br as $k => $b)
                                        <tr>
                                            <td>{{ $br->firstItem() + $k }}</td>
                                            <td>
                                                @if ($b->user && $b->user->f_name)
                                                    {{ $b->user->f_name . ' ' . $b->user->l_name }}
                                                @else
                                                    {{ $b->user->id ?? '-' }}
                                                @endif
                                            </td>

                                            <td>{{ $b->product_name ?? '-' }}</td>
                                            <td>{{ $b->quantity }}</td>
                                            <td>{{ $b->remark ?? '-' }}</td>
                                            <td>{{ $b->created_at->format('d M Y') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">
                                                {{ \App\CPU\translate('No_data_to_show') }}</td>
                                        </tr>
                                    @endforelse

                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="table-responsive mt-4">
                        <div class="d-flex justify-content-lg-end">
                            {{ $br->links() }}
                        </div>
                    </div>

                    @if ($br->isEmpty())
                        <div class="text-center p-4">
                            <img class="mb-3 w-160" src="{{ asset('public/assets/back-end') }}/svg/illustrations/sorry.svg"
                                alt="Image Description">
                            <p class="mb-0">{{ \App\CPU\translate('No_data_to_show') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
