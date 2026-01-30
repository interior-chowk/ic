@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('Category'))

@push('css_or_js')
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex gap-10">
                <img src="{{ asset('/public/assets/back-end/img/brand-setup.png') }}" alt="">
                {{ \App\CPU\translate('Category') }} {{ \App\CPU\translate('Setup') }}
            </h2>
        </div>
        <!-- End Page Title -->


        <div class="row mt-20" id="cate-table">
            <div class="col-md-12">
                <div class="card">
                    <div class="px-3 py-4">
                        <div class="row align-items-center">
                            <div class="col-sm-4 col-md-6 col-lg-8 mb-2 mb-sm-0">
                                <h5 class="text-capitalize d-flex gap-1">
                                    {{ \App\CPU\translate('category_request_list') }}
                                    <span class="badge badge-soft-dark radius-50 fz-12"></span>
                                </h5>
                            </div>
                            <div class="col-sm-8 col-md-6 col-lg-4">

                                <!-- End Search -->
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table style="text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};"
                            class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                            <thead class="thead-light thead-50 text-capitalize">
                                <tr>
                                    <th>{{ \App\CPU\translate('SELLER ID') }}</th>
                                    <!-- <th class="text-center">{{ \App\CPU\translate('Category') }} {{ \App\CPU\translate('Image') }}</th> -->
                                    <th>{{ \App\CPU\translate('name') }}</th>

                                    <th class="text-center">{{ \App\CPU\translate('HSN_CODE') }}</th>
                                    <th class="text-center">{{ \App\CPU\translate('CREATED') }}</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($categories as $key => $category)
                                    <tr>

                                        <td><a
                                                href="{{ url('admin/sellers/view/' . $category->seller_id) }}">{{ $category->seller_id }}</a>
                                        </td>

                                        <td>{{ $category->name }}</td>
                                        <td>
                                            {{ $category->hsn_code }}
                                        </td>
                                        <td class="text-center">
                                            {{ $category->created_at }}
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>



                </div>
            </div>
        </div>
    @endsection

    @push('script')
        <script>
            $(function() {
                $('[data-toggle="tooltip"]').tooltip()
            })
        </script>


        <script>
            $(document).on('change', '.category-status', function() {
                var id = $(this).attr("id");
                if ($(this).prop("checked") == true) {
                    var status = 1;
                } else if ($(this).prop("checked") == false) {
                    var status = 0;
                }
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "{{ route('admin.category.status') }}",
                    method: 'POST',
                    data: {
                        id: id,
                        home_status: status
                    },
                    success: function(data) {
                        if (data.success == true) {
                            toastr.success('{{ \App\CPU\translate('Status updated successfully') }}');
                        }
                    }
                });
            });
        </script>
        <script>
            $(document).on('click', '.delete', function() {
                var id = $(this).attr("id");
                Swal.fire({
                    title: '{{ \App\CPU\translate('Are_you_sure') }}?',
                    text: "{{ \App\CPU\translate('You_will_not_be_able_to_revert_this') }}!",
                    showCancelButton: true,
                    type: 'warning',
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: '{{ \App\CPU\translate('Yes') }}, {{ \App\CPU\translate('delete_it') }}!',
                    reverseButtons: true
                }).then((result) => {
                    if (result.value) {
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                            }
                        });
                        $.ajax({
                            url: "{{ route('admin.category.delete') }}",
                            method: 'POST',
                            data: {
                                id: id
                            },
                            success: function() {
                                toastr.success(
                                    '{{ \App\CPU\translate('Category_deleted_Successfully.') }}'
                                    );
                                location.reload();
                            }
                        });
                    }
                })
            });
        </script>

        <script>
            function readURL(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function(e) {
                        $('#viewer').attr('src', e.target.result);
                    }

                    reader.readAsDataURL(input.files[0]);
                }
            }

            $("#customFileEg1").change(function() {
                readURL(this);
            });
        </script>
        {{-- ck editor --}}
        <script src="{{ asset('/') }}vendor/ckeditor/ckeditor/ckeditor.js"></script>
        <script src="{{ asset('/') }}vendor/ckeditor/ckeditor/adapters/jquery.js"></script>
        <script>
            $('.textarea').ckeditor({
                contentsLangDirection: '{{ Session::get('direction') }}',
            });
        </script>
        {{-- ck editor --}}
    @endpush
