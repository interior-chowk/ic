@extends('layouts.back-end.app-seller')
@section('title', \App\CPU\translate('Shop view'))
@push('css_or_js')
    <!-- Custom styles for this page -->
    <link href="{{ asset('public/assets/back-end') }}/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                <img width="20" src="{{ asset('/public/assets/back-end/img/shop-info.png') }}" alt="">
                {{ \App\CPU\translate('Shop_Info') }}
            </h2>
        </div>
        <!-- End Page Title -->

        <div class="card mb-3">
            <div class="card-body">
                <div class="border rounded border-color-c1 px-4 py-3 d-flex justify-content-between mb-1">
                    <h5 class="mb-0 d-flex gap-1 c1">
                        {{ \App\CPU\translate('temporary_close') }}
                    </h5>
                    <div class="position-relative">
                        <label class="switcher">
                            <input type="checkbox" class="switcher_input" id="temporary_close"
                                {{ $shop->temporary_close == 1 ? 'checked' : '' }}>
                            <span class="switcher_control"></span>
                        </label>
                    </div>
                </div>
                <p>*{{ \App\CPU\translate('By turning on (temporary close) mode your shop will be temporary off on the website for the customers & they cannot purchase or place order from your shop') }}
                </p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div>
                            <h4 class="mb-0">{{ \App\CPU\translate('my_shop_info') }} </h4>
                        </div>
                        <div class="d-inline-flex gap-2">
                            <button class="btn btn-block __inline-70" data-toggle="modal" data-target="#balance-modal">
                                {{ \App\CPU\translate('go_to_Vacation_Mode') }}
                            </button>

                            <a class="btn btn--primary __inline-70 px-4 text-white"
                                href="{{ route('seller.shop.edit', [$shop->id]) }}">
                                {{ \App\CPU\translate('edit') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center flex-wrap gap-5">
                            @if ($shop->image == 'def.png')
                                <div class="text-{{ Session::get('direction') === 'rtl' ? 'right' : 'left' }}">
                                    {{-- <img height="200" width="200" class="rounded-circle border"
                                        onerror="this.src='{{ asset('public/assets/front-end/img/image-place-holder.png') }}'"
                                        src="{{ asset('public/assets/back-end') }}/img/shop.png"> --}}
                                    <div class="rounded-circle border"
                                        style="height:200px;width:200px;background:#073b74;color:#fff;font-size: 90px;padding-top: 35px;padding-left: 35px;">
                                        <?php
                                        $text = '';
                                        
                                        if (!empty($shop->comp_name ?? $shop->name)) {
                                            $words = preg_split('/\s+/', trim($shop->comp_name ?? $shop->name));
                                        
                                            if (count($words) >= 2) {
                                                $text = strtoupper(mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1));
                                            } else {
                                                $text = strtoupper(mb_substr($words[0], 0, 2));
                                            }
                                        }
                                        
                                        echo $text;
                                        ?>
                                    </div>
                                </div>
                            @else
                                <div class="text-{{ Session::get('direction') === 'rtl' ? 'right' : 'left' }}">
                                    {{-- <img 
                                    src="{{ asset('storage/app/public/shop/' . $shop->image) }}"
                                        class="rounded-circle border" height="200" width="200" alt=""> --}}
                                    <div class="avatar-initials rounded-circle border"
                                        style="height:200px;width:200px;background:#073b74;color:#fff;font-size: 90px;padding-top: 35px;padding-left: 35px;">
                                        <?php
                                        $text = '';
                                        
                                        if (!empty($shop->comp_name ?? $shop->name)) {
                                            $words = preg_split('/\s+/', trim($shop->comp_name ?? $shop->name));
                                        
                                            if (count($words) >= 2) {
                                                $text = strtoupper(mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1));
                                            } else {
                                                $text = strtoupper(mb_substr($words[0], 0, 2));
                                            }
                                        }
                                        
                                        echo $text;
                                        ?>
                                    </div>
                                </div>
                            @endif

                            <div class="">
                                <div class="flex-start">
                                    <h4>{{ \App\CPU\translate('Name') }} : </h4>
                                    <h4 class="mx-1">{{ $shop->name }}</h4>
                                </div>
                                <div class="flex-start">
                                    <h6>{{ \App\CPU\translate('Phone') }} : </h6>
                                    <h6 class="mx-1">{{ $shop->contact }}</h6>
                                </div>
                                <div class="flex-start">
                                    <h6>{{ \App\CPU\translate('Billing / Registered') }} : </h6>
                                    <h6 class="mx-1">{{ $shop->address }}</h6>
                                </div>
                            </div>
                            <div class=""></div>
                        </div>
                    </div>



                    <div class="card-body p-30">
                        <div class="row justify-content-center">
                            <div class="col-sm-6 col-md-8 col-lg-6 col-xl-5">
                                <!-- Bank Info Card -->
                                <div class="card bank-info-card bg-bottom bg-contain bg-img"
                                    style="background-image: url({{ asset('/public/assets/back-end/img/bank-info-card-bg.png') }});">
                                    <div class="border-bottom p-3">
                                        <h4 class="mb-0 fw-semibold">{{ \App\CPU\translate('Holder_Name') }} :
                                            {{ $shop->bank_holder_name ?? 'No Data found' }}
                                        </h4>
                                    </div>

                                    <div class="card-body position-relative">
                                        <img class="bank-card-img" width="78"
                                            src="{{ asset('/public/assets/back-end/img/bank-card.png') }}" alt="">

                                        <ul class="list-unstyled d-flex flex-column gap-4">
                                            <li>
                                                <h3 class="mb-2">{{ \App\CPU\translate('Bank_Name') }} :</h3>
                                                <div>{{ $shop->bank_name ?? 'No Data found' }}</div>
                                            </li>
                                            <li>
                                                <h3 class="mb-2">{{ \App\CPU\translate('IFSC') }} :</h3>
                                                <div>{{ $shop->ifsc ?? 'No Data found' }}</div>
                                            </li>
                                            <li>
                                                <h3 class="mb-2">{{ \App\CPU\translate('Account_Number') }} : </h3>
                                                <div>{{ $shop->acc_no ?? 'No Data found' }}</div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <!-- End Bank Info Card -->
                            </div>
                        </div>
                    </div>
                </div>




            </div>
        </div>

        <div class="modal fade" id="balance-modal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content"
                    style="text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};">
                    <form action="{{ route('seller.shop.vacation-add', [$shop->id]) }}" method="post">
                        <div class="modal-header border-bottom pb-2">
                            <div>
                                <h5 class="modal-title" id="exampleModalLabel">{{ \App\CPU\translate('Vacation_Mode') }}
                                </h5>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="switcher">
                                        <input type="checkbox" name="vacation_status" class="switcher_input"
                                            id="vacation_close" {{ $shop->vacation_status == 1 ? 'checked' : '' }}>
                                        <span class="switcher_control"></span>
                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <button type="button" class="close pt-0" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="modal-body">
                            <div class="mb-5">
                                *{{ \App\CPU\translate('set_vacation_mode_for_shop_means_you_will_be_not_available_receive_order_and_provider_products_for_placed_order_at_that_time') }}
                            </div>

                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <label>{{ \App\CPU\translate('Vacation_Start') }}</label>
                                    <input type="date" name="vacation_start_date"
                                        value="{{ $shop->vacation_start_date }}" id="vacation_start_date"
                                        class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label>{{ \App\CPU\translate('Vacation_End') }}</label>
                                    <input type="date" name="vacation_end_date" value="{{ $shop->vacation_end_date }}"
                                        id="vacation_end_date" class="form-control" required>
                                </div>
                                <div class="col-md-12 mt-2 ">
                                    <label>{{ \App\CPU\translate('Vacation_Note') }}</label>
                                    <textarea class="form-control" name="vacation_note" id="vacation_note">{{ $shop->vacation_note }}</textarea>
                                </div>
                            </div>

                            <div class="text-end gap-5 mt-2">
                                <button type="button" class="btn btn-secondary"
                                    data-dismiss="modal">{{ \App\CPU\translate('Close') }}</button>
                                <button type="submit"
                                    class="btn btn--primary">{{ \App\CPU\translate('update') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $('#temporary_close').on('change', function() {
            let status = $(this).prop("checked") === true ? 'checked' : 'unchecked';
            Swal.fire({
                title: '{{ \App\CPU\translate('Are you sure Change this') }}?',
                text: "",
                showCancelButton: true,
                confirmButtonColor: '#377dff',
                cancelButtonColor: 'secondary',
                confirmButtonText: '{{ \App\CPU\translate('Yes, Change it') }}!'
            }).then((result) => {
                if (result.value) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{ route('seller.shop.temporary-close') }}",
                        method: 'POST',
                        data: {
                            id: '{{ $shop->id }}',
                            status: status
                        },
                        success: function(data) {
                            toastr.success(
                                '{{ \App\CPU\translate('temporary_close_inactive_successfully') }}!'
                            );
                            location.reload();
                        }
                    });
                }
            });
        });

        $('#vacation_start_date,#vacation_end_date').change(function() {
            let fr = $('#vacation_start_date').val();
            let to = $('#vacation_end_date').val();
            if (fr != '') {
                $('#vacation_end_date').attr('required', 'required');
            }
            if (to != '') {
                $('#vacation_start_date').attr('required', 'required');
            }
            if (fr != '' && to != '') {
                if (fr > to) {
                    $('#vacation_start_date').val('');
                    $('#vacation_end_date').val('');
                    toastr.error('{{ \App\CPU\translate('Invalid date range') }}!', Error, {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            }

        })
    </script>
@endpush
