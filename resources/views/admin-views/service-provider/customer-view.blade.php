@extends('layouts.back-end.app')
{{--@section('title','Customer')--}}
@section('title', \App\CPU\translate('Customer Details'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="d-print-none pb-2">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">

                    <!-- Page Title -->
                    <div class="mb-3">
                        <h2 class="h1 mb-0 text-capitalize d-flex gap-2">
                            <img width="20" src="{{asset('/public/assets/back-end/img/customer.png')}}" alt="">
                            {{\App\CPU\translate('service_provider_details')}}
                        </h2>
                    </div>
                    <!-- End Page Title -->

                    <div class="d-sm-flex align-items-sm-center">
                        <h3 class="page-header-title">{{\App\CPU\translate('Service Provider ID')}} #{{$customer['id']}}</h3>
                        <span class="{{Session::get('direction') === "rtl" ? 'mr-2 mr-sm-3' : 'ml-2 ml-sm-3'}}">
                        <i class="tio-date-range">
                        </i> {{\App\CPU\translate('Joined At')}} : {{date('d M Y H:i:s',strtotime($customer['created_at']))}}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Page Header -->

        <div class="row" id="printableArea">
            <div class="col-lg-8 mb-3 mb-lg-0">
                <div class="card">
                    <div class="p-3">
                        <div class="row justify-content-end">
                            <div class="col-auto">
                                <form action="{{ url()->current() }}" method="GET">
                                    <!-- Search -->
                                    <div class="input-group input-group-merge input-group-custom">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="tio-search"></i>
                                            </div>
                                        </div>
                                        <input id="datatableSearch_" type="search" name="search" class="form-control"
                                            placeholder="{{\App\CPU\translate('Search orders')}}" aria-label="Search orders" value="{{ $search }}"
                                            required>
                                        <button type="submit" class="btn btn--primary">{{\App\CPU\translate('search')}}</button>
                                    </div>
                                    <!-- End Search -->
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- Table -->
                    <div class="table-responsive datatable-custom">
                        <table
                               class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                            <thead class="thead-light thead-50 text-capitalize">
                            <tr>
                                <th>{{\App\CPU\translate('sl')}}</th>
                                <th>{{\App\CPU\translate('Order ID')}}</th>
                                <th>{{\App\CPU\translate('Total')}}</th>
                                <th class="text-center">{{\App\CPU\translate('Action')}}</th>
                            </tr>

                            </thead>

                            <tbody>
                            @foreach($orders as $key=>$order)
                                <tr>
                                    <td>{{$orders->firstItem()+$key}}</td>
                                    <td>
                                        <a href="{{route('admin.orders.details',['id'=>$order['id']])}}" class="title-color hover-c1">{{$order['id']}}</a>
                                    </td>
                                    <td> {{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($order['order_amount']))}}</td>

                                    <td>
                                        <div class="d-flex justify-content-center gap-10">
                                            <a class="btn btn-outline--primary btn-sm edit square-btn"
                                                title="{{\App\CPU\translate('View')}}"
                                                href="{{route('admin.orders.details',['id'=>$order['id']])}}"><i
                                                    class="tio-invisible"></i> </a>
                                            <a class="btn btn-outline-info btn-sm square-btn"
                                                title="{{\App\CPU\translate('Invoice')}}"
                                                target="_blank"
                                                href="{{route('admin.orders.generate-invoice',[$order['id']])}}"><i
                                                    class="tio-download"></i> </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        @if(count($orders)==0)
                            <div class="text-center p-4">
                                <img class="mb-3 w-160" src="{{asset('assets/back-end')}}/svg/illustrations/sorry.svg" alt="Image Description">
                                <p class="mb-0">{{ \App\CPU\translate('No_data_to_show')}}</p>
                            </div>
                        @endif
                        <!-- Footer -->
                        <div class="card-footer">
                            <!-- Pagination -->
                        {!! $orders->links() !!}
                        <!-- End Pagination -->
                        </div>
                        <!-- End Footer -->
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <!-- Card -->
                <div class="card">
                    @if($customer)
                        <div class="card-body">
                            <h4 class="mb-4 d-flex align-items-center gap-2">
                                <img src="{{asset('/public/assets/back-end/img/seller-information.png')}}" alt="">
                                {{\App\CPU\translate('Service Provider')}}
                            </h4>

                            <div class="media">
                                <div class="mr-3">
                                    <img
                                        class="avatar rounded-circle avatar-70"
                                        onerror="this.src='{{asset('assets/front-end/img/image-place-holder.png')}}'"
                                        src="{{asset('storage/service-provider/profile/'.$customer->image??'')}}"
                                        alt="Image">
                                </div>
                                <div class="media-body d-flex flex-column gap-1">
                                    <span class="title-color hover-c1"><strong>{{$customer['f_name'].' '.$customer['l_name']}}</strong></span>
                                    <span class="title-color">
                                        <strong>{{\App\Model\Order::where('customer_id',$customer['id'])->count()}} </strong>{{\App\CPU\translate('orders')}}
                                    </span>
                                    <span class="title-color"><strong>{{$customer['phone']}}</strong></span>
                                    <span class="title-color">{{$customer['email']}}</span>
                                </div>
                                <div class="media-body text-right">
                                    {{--<i class="tio-chevron-right text-body"></i>--}}
                                </div>
                            </div>
                        </div>
                @endif

                <!-- End Body -->
                </div>
                <!-- End Card -->
                
                     <!--start  personal details--->
            
                     
                    <div class="card mt-2">
                        <div class="card-header">
                            <h4 class="mb-0">{{\App\CPU\translate('personal_details')}}</h4>
                        </div>
                        <div class="card-body"
                             style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                            <div class="flex-start">
                                <div><h5>{{\App\CPU\translate('name')}} : </h5></div>
                                <div class="mx-1"><h5>{{$customer->name}}</h5></div>
                            </div>
                             <div class="flex-start">
                                <div><h5>{{\App\CPU\translate('date_of_birth')}} : </h5></div>
                                <div class="mx-1"><h5>{{$customer->dob}}</h5></div>
                            </div>
                            <div class="flex-start">
                                <div><h5>{{\App\CPU\translate('father_name')}} : </h5></div>
                                <div class="mx-1"><h5>{{$customer->father_name}}</h5></div>
                            </div>
                           
                            <div class="flex-start">
                                <div><h5>{{\App\CPU\translate('aadhar_number')}} : </h5></div>
                                <div class="mx-1"><h5>{{$customer->adhaar_number}}
                            </h5></div>
                            </div>
                            
                             <div class="flex-start">
                                <div><h5>{{\App\CPU\translate('aadhar_photo')}} : </h5></div>
                                <div class="mx-1"><h5>
                                    <img
                                        class="avatar " style="width: auto;"
                                        onerror="this.src='{{asset('assets/front-end/img/image-place-holder.png')}}'"
                                        src="{{asset('storage/service-provider/'.$customer->adhaar_front_image??'')}}"
                                        alt="Image">
                                    
                                    <img
                                        class="avatar " style="width: auto;"
                                        onerror="this.src='{{asset('assets/front-end/img/image-place-holder.png')}}'"
                                        src="{{asset('storage/service-provider/'.$customer->adhaar_back_image??'')}}"
                                        alt="Image">
                                    
                            </h5></div>
                            </div>
                            
                             <div class="flex-start">
                                <div><h5>{{\App\CPU\translate('current_address')}} : </h5></div>
                                <div class="mx-1"><h5>{{$customer->current_address}}
                            </h5></div>
                            </div>
                            
                             <div class="flex-start">
                                <div><h5>{{\App\CPU\translate('permanent_address')}} : </h5></div>
                                <div class="mx-1"><h5>{{$customer->permanent_address}}
                            </h5></div>
                            </div>


                        </div>
                    </div>
              
            
            <!--End  personal details--->
            
            <!-- start work details --->
            @if($customer->role == 2)
            
                  <div class="card mt-2">
                        <div class="card-header">
                            <h4 class="mb-0">{{\App\CPU\translate('work_details')}}</h4>
                        </div>
                        <div class="card-body"
                             style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                            <div class="flex-start">
                                <div><h5>{{\App\CPU\translate('work_role')}} : </h5></div>
                                <div class="mx-1"><h5>{{$customer->work_role}}</h5></div>
                            </div>
                            
                            @if($customer->work_role != 'helper')
                             <div class="flex-start">
                                <div><h5>{{\App\CPU\translate('tools')}} : </h5></div>
                                <div class="mx-1"><h5>{{$customer->tools}}</h5></div>
                            </div>
                            @endif
                            
                            <div class="flex-start">
                                <div><h5>{{\App\CPU\translate('work_types')}} : </h5></div>
                                <div class="mx-1"><h5>{{$customer->type_of_work}}</h5></div>
                            </div>
                           

                        </div>
                    </div>
            
                <div class="card mt-2">
                        <div class="card-header">
                            <h4 class="mb-0">{{\App\CPU\translate('payout_method')}}</h4>
                        </div>
                        <div class="card-body"
                             style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                            <div class="flex-start">
                                <div><h5>{{\App\CPU\translate('payout')}} : </h5></div>
                                <div class="mx-1"><h5>{{$customer->payout_ways}}</h5></div>
                            </div>
                             <div class="flex-start">
                                <div><h5>{{\App\CPU\translate('payout_method')}} : </h5></div>
                                <div class="mx-1"><h5>{{$customer->payout_amount}}rs.</h5></div>
                            </div>
                            
                        </div>
                    </div>
            
            @endif
            
             @if($customer->role == 3)
             <div class="card mt-2">
                        <div class="card-header">
                            <h4 class="mb-0">{{\App\CPU\translate('work_details')}}</h4>
                        </div>
                        <div class="card-body"
                             style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                            <div class="flex-start">
                                <div><h5>{{\App\CPU\translate('work_types')}} : </h5></div>
                                <div class="mx-1"><h5>{{$customer->type_of_work}}</h5></div>
                            </div>
                           

                        </div>
                    </div>
            
            @endif
            <!-- end work details --->
            
            
            <!-- start area of the working-->
            
            
                  
                  <div class="card mt-2">
                        <div class="card-header">
                            <h4 class="mb-0">{{\App\CPU\translate('area_of_working')}}</h4>
                        </div>
                        <div class="card-body"
                             style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                            <div class="flex-start">
                                <div><h5>{{\App\CPU\translate('state')}} : </h5></div>
                                <div class="mx-1"><h5>{{$customer->state}}</h5></div>
                            </div>
                             <div class="flex-start">
                                <div><h5>{{\App\CPU\translate('distric')}} : </h5></div>
                                <div class="mx-1"><h5>{{$customer->distric}}</h5></div>
                            </div>
                            <div class="flex-start">
                                <div><h5>{{\App\CPU\translate('city')}} : </h5></div>
                                <div class="mx-1"><h5>{{$customer->city}}</h5></div>
                            </div>
                            <div class="flex-start">
                                <div><h5>{{\App\CPU\translate('working_location')}} : </h5></div>
                                <div class="mx-1"><h5>{{$customer->working_location}}</h5></div>
                            </div>
                            <div class="flex-start">
                                <div><h5>{{\App\CPU\translate('radius_of_working')}} : </h5></div>
                                <div class="mx-1"><h5>{{$customer->radius_of_working_area_in_km}} km</h5></div>
                            </div>
                            <div class="flex-start">
                                <div><h5>{{\App\CPU\translate('refrence_1')}} : </h5></div>
                                <div class="mx-1"><h5>{{$customer->refrence_1}}</h5></div>
                            </div>
                            <div class="flex-start">
                                <div><h5>{{\App\CPU\translate('refrence_2')}} : </h5></div>
                                <div class="mx-1"><h5>{{$customer->refrence_2}}</h5></div>
                            </div>
                           

                        </div>
                    </div>
            
            
            <!-- end area of the working-->
            
            
            </div>
       
        </div>
        <!-- End Row -->
    </div>
@endsection

@push('script_2')

@endpush
