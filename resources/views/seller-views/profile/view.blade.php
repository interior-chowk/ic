@extends('layouts.back-end.app-seller')
@section('title', \App\CPU\translate('Bank Info View'))
@push('css_or_js')
    <!-- Custom styles for this page -->
    <link href="{{asset('assets/back-end')}}/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                <img width="20" src="{{asset('/public/assets/back-end/img/my-bank-info.png')}}" alt="">
                {{\App\CPU\translate('my_bank_info')}}
            </h2>
        </div>
        <!-- End Page Title -->

        <div class="row mt-3">
            <div class="col-md-12">
                <div class="card" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                    <!-- Card Header -->
                    <div class="border-bottom d-flex gap-3 flex-wrap justify-content-between align-items-center px-4 py-3">
                        <div class="d-flex gap-2 align-items-center">
                            <img width="20" src="{{asset('/public/assets/back-end/img/bank.png')}}" alt="" />
                            <h3 class="mb-0">{{\App\CPU\translate('Bank Information')}}</h3>
                        </div>

                        <div class="d-flex gap-2 align-items-center">
                            <a href="{{route('seller.profile.bankInfo',[$data->id])}}" class="btn btn--primary">
                                {{\App\CPU\translate('Edit')}}
                            </a>
                        </div>
                    </div>
                    <!-- End Card Header -->

                    <!-- Card Body -->
                    <div class="card-body p-30">
                        <div class="row justify-content-center">
                            <div class="col-sm-6 col-md-8 col-lg-6 col-xl-5">
                                <!-- Bank Info Card -->
                                <div class="card bank-info-card bg-bottom bg-contain bg-img" style="background-image: url({{asset('/public/assets/back-end/img/bank-info-card-bg.png')}});">
                                    <div class="border-bottom p-3">
                                        <h4 class="mb-0 fw-semibold">{{\App\CPU\translate('Holder_Name')}} : <strong>{{$data->holder_name ?? 'No Data found'}}</strong></h4>
                                    </div>

                                    <div class="card-body position-relative">
                                        <img class="bank-card-img" width="78" src="{{asset('/public/assets/back-end/img/bank-card.png')}}" alt="">

                                        <ul class="list-unstyled d-flex flex-column gap-4">
                                            <li>
                                                <h3 class="mb-2">{{\App\CPU\translate('Bank_Name')}} :</h3>
                                                <div>{{$data->shop->bank_name ?? 'No Data found'}}</div>
                                            </li>
                                            <li>
                                                <h3 class="mb-2">{{\App\CPU\translate('Branch_Name')}} :</h3>
                                                <div>{{$data->shop->bank_branch ?? 'No Data found'}}</div>
                                            </li>
                                            <li>
                                                <h3 class="mb-2">{{\App\CPU\translate('Account_Number')}} : </h3>
                                                <div>{{$data->shop->acc_no ?? 'No Data found'}}</div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <!-- End Bank Info Card -->
                            </div>
                        </div>
                    </div>
                    <!-- End Card Body -->
                </div>
                
                
                  <div class="card mt-2">
                    <div class="card-header">
                        <h4 class="mb-0 ">{{\App\CPU\translate('cheque')}}</h4>
                    </div>
                    
                    <div class="card-body">
                       
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12 mb-3 text-center">
                                        <?php 
                                        $chequeImagePath = 'storage/shop/' .$data->shop->cheque_image;
                                        $chequeImageExtension = pathinfo($chequeImagePath, PATHINFO_EXTENSION);
                                        
                                        ?>
                                         @if ($chequeImageExtension != 'pdf')
                                     <img src="{{asset('storage/shop/'.$data->shop->cheque_image)}}"
                                         onerror="this.src='{{asset('assets/front-end/img/image-place-holder.png')}}'"
                                         height="200"  alt="">
                                         @else
                                         <iframe src="{{asset('storage/shop/'.$data->shop->cheque_image)}}" style="width:600px; height:500px;" frameborder="0"></iframe>
                                         @endif
                                    </div>
                                  
                                </div>

                            </div>

                         
                    </div>
                    
                    
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script')
    <!-- Page level plugins -->
@endpush
