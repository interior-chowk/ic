@extends('layouts.back-end.app-seller')

@section('title', \App\CPU\translate('Signature')) 

@push('css_or_js')
    <!-- Custom styles for this page -->
    <link href="{{asset('public/assets/back-end')}}/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <!-- Custom styles for this page -->
    <link href="{{asset('public/assets/back-end/css/croppie.css')}}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
        <!-- Page Title -->
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                <img width="20" src="{{asset('/public/assets/back-end/img/my-bank-info.png')}}" alt="">
                {{\App\CPU\translate('Signature')}}
            </h2>
        </div>
        <!-- End Page Title -->

        <!-- Content Row -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0 ">{{\App\CPU\translate('signature')}}</h4>
                    </div>
                    
                    <div class="card-body">
                        <form action="{{route('seller.profile.Signature_update',[$data->id])}}" method="post"
                              enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12 mb-3 text-center">
                                      <img src="{{asset('storage/app/public/seller/'.$data->signature)}}"
                                         onerror="this.src='{{asset('public/assets/front-end/img/image-place-holder.png')}}'"
                                         height="200"  alt="">
                                    </div>
                                  
                                    <div class="col-md-12 mb-3">
                                        <label for="name" class="title-color">{{\App\CPU\translate('Upload Signature')}} </label>
                                        <input type="file" name="signature" value=""
                                               class="form-control" id="Signature_id"
                                               required>
                                    </div>

                                </div>

                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a class="btn btn-danger" href="{{route('seller.profile.view')}}">{{\App\CPU\translate('Cancel')}}</a>
                                <button type="submit" class="btn btn--primary" id="btn_update">{{\App\CPU\translate('Update')}}</button>
                            </div>
                        </form>
                    </div>
                    
                    
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')

@endpush
