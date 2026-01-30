@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('Membership Report'))

@push('css_or_js')
    <link href="{{ asset('public/assets/select2/css/select2.min.css')}}" rel="stylesheet">
    <link href="{{ asset('public/assets/back-end/css/custom.css')}}" rel="stylesheet">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                <img src="{{asset('/public/assets/back-end/img/coupon_setup.png')}}" alt="">
                {{\App\CPU\translate('Report')}}
            </h2>
        </div>
        <!-- End Page Title -->

        <div class="row mt-20">
            <div class="col-md-12">
                <div class="card">
                    <div class="px-3 py-4">
                        <div class="row justify-content-between align-items-center flex-grow-1">
                            <div class="col-sm-4 col-md-6 col-lg-8 mb-2 mb-sm-0">
                                <h5 class="mb-0 text-capitalize d-flex gap-2">
                                    {{\App\CPU\translate('Report_list')}}
                                    <span class="badge badge-soft-dark radius-50 fz-12 ml-1">{{ $mem->total() }}</span>
                                </h5>
                            </div>
                            <div class="col-sm-8 col-md-6 col-lg-4">
                                <!-- Search -->
                                <form action="{{ url()->current() }}" method="GET">
                                    <div class="input-group input-group-merge input-group-custom">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="tio-search"></i>
                                            </div>
                                        </div>
                                        <input id="datatableSearch_" type="search" name="search" class="form-control"
                                               placeholder="{{\App\CPU\translate('Search by Plan or Price or Reward value')}}"
                                               value="{{ $search }}" aria-label="Search orders" required>
                                        <button type="submit" class="btn btn--primary">{{\App\CPU\translate('search')}}</button>
                                    </div>
                                </form>
                                <!-- End Search -->
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="datatable"
                                class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table {{ Session::get('direction') === 'rtl' ? 'text-right' : 'text-left' }}">
                            <thead class="thead-light thead-50 text-capitalize">
                            <tr>
                                <th>{{\App\CPU\translate('SL')}}</th>
                                <th>{{\App\CPU\translate('Provider Name')}}</th>
                                <th>{{\App\CPU\translate('Plan Name')}}</th>
                                <th>{{\App\CPU\translate('Price')}}</th>
                                <th>{{\App\CPU\translate('Transaction_id')}}</th>
                                <th class="text-center">{{\App\CPU\translate('Action')}}</th>                               
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($mem as $m)
                            @if($m->membership)
                            @php $subtotal=($m->membership->price+(($m->membership->price*18)/100)) @endphp
                            @endif
                                <tr>
                                    <td >{{ $m->id}}</td>
                                    <td>
                                       <strong> <div>{{ ($m->provider) ? $m->provider->name : '' }}</div></strong>
                                        
                                    </td>
                                    <td>
                                       <strong> <div>{{ ($m->membership) ? $m->membership->plan_name : '' }}</div></strong>
                                        
                                    </td>
                                    <td class="text-capitalize">{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency(($m->membership) ? $m->membership->price : ''))}}</td>
                                    <td>
                                       {{ ($m) ? $m->transaction_id : '' }}
                                    </td>
                                    
                                    <td>
                                        <div class="d-flex  justify-content-center">
                                           @if($m->membership == ! null)
                                            <a class="btn btn-outline-success square-btn btn-sm mr-1" target="_blank" title="{{\App\CPU\translate('invoice')}}"
                                                href="{{route('admin.Membership_plan.generate-invoice',$m['id'])}}">
                                                <i class="tio-file"></i>
                                            </a>
                                            @else
                                            <span class="btn btn-outline-success square-btn btn-sm mr-1 disabled" 
                                                  title="{{ \App\CPU\translate('invoice') }}" 
                                                  style="cursor: not-allowed;">
                                                <i class="tio-file"></i>
                                            </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <div class="modal fade" id="quick-view" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered coupon-details" role="document">
                                <div class="modal-content" id="quick-view-modal">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive mt-4">
                        <div class="px-4 d-flex justify-content-lg-end">
                            <!-- Pagination -->
                            {{$mem->links()}}
                        </div>
                    </div>

                    @if(count($mem)==0)
                        <div class="text-center p-4">
                            <img class="mb-3 w-160" src="{{asset('public/assets/back-end')}}/svg/illustrations/sorry.svg"
                                 alt="Image Description">
                            <p class="mb-0">{{\App\CPU\translate('No data to show')}}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
       
    </div>
@endsection

@push('script')
    <!-- Add your scripts here -->
@endpush
