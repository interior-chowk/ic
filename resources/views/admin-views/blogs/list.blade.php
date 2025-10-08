@extends('layouts.back-end.app')
@section('title', \App\CPU\translate('Blog List'))

@push('css_or_js')
    <link href="{{asset('assets/back-end/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
@endpush

@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
            <img src="{{asset('/public/assets/back-end/img/blog.png')}}" width="20" alt="">
            {{\App\CPU\translate('blog_list')}}
        </h2>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header flex-wrap gap-10">
                    <h5 class="mb-0 d-flex gap-2 align-items-center">
                        {{\App\CPU\translate('blog_table')}}
                        <span class="badge badge-soft-dark radius-50 fz-12">{{$blogs->total()}}</span>
                    </h5>

                    <form action="{{ url()->current() }}" method="GET">
                        <div class="input-group input-group-merge input-group-custom">
                            <div class="input-group-prepend">
                                <div class="input-group-text"><i class="tio-search"></i></div>
                            </div>
                            <input type="search" name="search" class="form-control"
                                   placeholder="{{\App\CPU\translate('search_by_title_or_category')}}"
                                   value="{{request('search')}}" required>
                            <button type="submit" class="btn btn--primary">{{\App\CPU\translate('search')}}</button>
                        </div>
                    </form>

                    <div class="d-flex justify-content-end">
                        <a href="{{route('admin.blog.create')}}" class="btn btn--primary">
                            <i class="tio-add"></i>
                            <span class="text">{{\App\CPU\translate('Add')}} {{\App\CPU\translate('New_Blog')}}</span>
                        </a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="datatable" class="table table-hover table-borderless table-thead-bordered table-align-middle card-table w-100">
                        <thead class="thead-light thead-50 text-capitalize table-nowrap">
                            <tr>
                                <th>{{\App\CPU\translate('SL')}}</th>
                                <th>{{\App\CPU\translate('Title')}}</th>
                                <th>{{\App\CPU\translate('Slug')}}</th>
                                <th>{{\App\CPU\translate('Category')}}</th>
                                <th>{{\App\CPU\translate('Image')}}</th>
                                <th class="text-center">{{\App\CPU\translate('Action')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($blogs as $k => $blog)
                            <tr>
                                <td>{{ $k + 1 }}</td>
                                <td class="text-capitalize">{{ $blog->title }}</td>
                                <td>{{ $blog->slug }}</td>
                                <td>{{ $blog->category }}</td>
                                <td>
                                    <img src="{{ asset('storage/' . $blog->image) }}" alt="Blog Image" width="60">
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center">
                                        <a href="{{route('admin.blog.edit', $blog->id)}}" class="btn btn-outline--primary btn-sm square-btn mx-2" title="{{\App\CPU\translate('Edit')}}">
                                            <i class="tio-edit"></i>
                                        </a>
                                        <a class="btn btn-outline-danger btn-sm delete square-btn" title="{{\App\CPU\translate('Delete')}}" data-id="{{ $blog->id }}">
                                            <i class="tio-delete"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        @if($blogs->isEmpty())
                            <tr><td colspan="7" class="text-center">{{\App\CPU\translate('No data found')}}</td></tr>
                        @endif
                        </tbody>
                    </table>
                </div>

                <div class="table-responsive mt-4">
                    <div class="px-4 d-flex justify-content-lg-end">
                        {{ $blogs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    $(document).on('click', '.delete', function () {
        var id = $(this).data("id");

        Swal.fire({
            title: '{{ \App\CPU\translate('Are_you_sure_delete')}}?',
            text: "{{ \App\CPU\translate('You_will_not_be_able_to_revert_this')}}!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '{{ \App\CPU\translate('Yes')}}, {{ \App\CPU\translate('delete_it')}}!',
            cancelButtonText: "{{ \App\CPU\translate('cancel')}}",
            reverseButtons: true
        }).then((result) => {
            if (result.value) {
                $.ajaxSetup({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content') }
                });
                $.ajax({
                    url: "{{route('admin.blog.delete')}}",
                    method: 'POST',
                    data: { id: id },
                    success: function () {
                        toastr.success('{{ \App\CPU\translate('deleted_successfully')}}');
                        location.reload();
                    }
                });
            }
        });
    });
</script>
@endpush
