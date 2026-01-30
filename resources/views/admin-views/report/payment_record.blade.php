@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('Payment Record Report'))

@push('css_or_js')
@endpush

@section('content')
    <style>
        .__table thead th {
            padding-top: 0rem;
            padding-bottom: 0rem;
        }

        .__table tbody td {
            padding-top: .10rem;
            padding-bottom: .10rem;
        }
    </style>
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                <img width="20" src="{{ asset('/public/assets/back-end/img/order_report.png') }}" alt="">
                {{ \App\CPU\translate('Payment_Record_Report') }}
            </h2>
        </div>
        <!-- End Page Title -->

        <div class="card">
            <div class="card-header border-0">
                <div class="d-flex flex-wrap w-100 gap-3 align-items-center">
                    <h4 class="mb-0 mr-auto">
                        {{ \App\CPU\translate('Total_Orders') }}
                        <span class="badge badge-soft-dark radius-50 fz-14"></span>
                    </h4>
                    <form action="" method="GET" class="mb-0">
                        <!-- Search -->
                        <div class="input-group input-group-merge input-group-custom">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="tio-search"></i>
                                </div>
                            </div>

                            <input id="datatableSearch_" value="" type="search" name="search" class="form-control"
                                placeholder="{{ \App\CPU\translate('search_by_order_id') }}" aria-label="Search orders"
                                required>
                            <button type="submit" class="btn btn--primary">{{ \App\CPU\translate('search') }}</button>
                        </div>
                        <!-- End Search -->
                    </form>
                    <div>
                        <button type="button" class="btn btn-outline--primary text-nowrap btn-block"
                            data-toggle="dropdown">
                            <i class="tio-download-to"></i>
                            {{ \App\CPU\translate('export') }}
                            <i class="tio-chevron-down"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li>
                                <a class="dropdown-item"
                                    href="{{ route('admin.report.order-sale-report-excel', ['date_type' => request('date_type'), 'seller_id' => request('seller_id'), 'from' => request('from'), 'to' => request('to'), 'search' => request('search'), 'product_id' => request('product_id')]) }}">
                                    {{ \App\CPU\translate('excel') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table id="datatable" style="text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};"
                    class="table __table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                    <thead class="thead-light thead-50 text-capitalize">

                        <tr>
                            <th>{{ \App\CPU\translate('Vendor Code') }}</th>
                            <th>Seller Name</th>
                            <th>{{ \App\CPU\translate('Payment Against') }}</th>
                            <th>{{ \App\CPU\translate('Amount') }}</th>
                            <th>{{ \App\CPU\translate('Payment Mode') }}</th>
                            <th>{{ \App\CPU\translate('Payment Ref No.') }}</th>
                            <th>{{ \App\CPU\translate('Payment Bank Name') }}</th>
                            <th>{{ \App\CPU\translate('Payment Date') }}</th>
                            <th>{{ \App\CPU\translate('Narration') }}</th>
                            <th>{{ \App\CPU\translate('Status') }}</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($payment_record as $record)
                            <tr>
                                <td>VN{{ $record->seller_id ?? '' }}</td>
                                <td>{{ $record->name ?? '' }}</td>
                                <td>{{ $record->payment_against ?? '' }}</td>
                                <td>{{ $record->amount ?? '' }}</td>
                                <td>{{ $record->payment_mode ?? '' }}</td>
                                <td>{{ $record->payment_ref_no ?? '' }}</td>
                                <td>{{ $record->payment_bank_name ?? '' }}</td>
                                <td>{{ $record->payment_date ?? '' }}</td>
                                <td>{{ $record->narration ?? '' }}</td>
                                <td>Paid</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>

    </div>

@endsection
