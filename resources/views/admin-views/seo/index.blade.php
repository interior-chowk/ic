@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('SEO Pages'))

@push('css_or_js')
<!-- Custom styles for this page -->
<link href="{{ asset('assets/back-end') }}/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
@endpush

@section('content')
<div class="content container-fluid">
    <!-- Page Title -->
    <div class="mb-3">
        <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
            <img src="{{ asset('/public/assets/back-end/img/Pages.png') }}" width="20" alt="">
            {{ \App\CPU\translate('SEO Pages') }}
        </h2>
    </div>

    <!-- Optional inline menu if needed -->
    {{-- @include('admin-views.business-settings.pages-inline-menu') --}}

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ \App\CPU\translate('SEO Page Table') }}</h5>
                    <a href="{{ route('admin.seo.create') }}" class="btn btn--primary btn-icon-split">
                        <i class="tio-add"></i>
                        <span class="text">{{ \App\CPU\translate('Add New SEO Page') }}</span>
                    </a>
                </div>
                <div class="card-body px-0">
                    <table class="table table-hover table-borderless table-thead-bordered table-align-middle card-table w-100" id="dataTable">
                        <thead class="thead-light thead-50 text-capitalize">
                            <tr>
                                <th>{{ \App\CPU\translate('SL') }}</th>
                                <th>{{ \App\CPU\translate('Page') }}</th>
                                <th>{{ \App\CPU\translate('Meta Title') }}</th>
                                <th>{{ \App\CPU\translate('Meta Description') }}</th>
                                <th>{{ \App\CPU\translate('Canonical') }}</th>
                                <th class="text-center">{{ \App\CPU\translate('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($seoMeta as $index => $seo)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $seo->page }}</td>
                                    <td>{{ $seo->meta_title }}</td>
                                    <td>{{ $seo->meta_description }}</td>
                                    <td>{{ $seo->canonical }}</td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-10">
                                            <a href="{{ route('admin.seo.edit', $seo->id) }}" class="btn btn-outline--primary btn-sm" title="{{ \App\CPU\translate('Edit') }}">
                                                <i class="tio-edit"></i>
                                            </a>
                                            <form method="POST" action="{{ route('admin.seo.destroy', $seo->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm" title="{{ \App\CPU\translate('Delete') }}">
                                                    <i class="tio-delete"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                            @if($seoMeta->isEmpty())
                                <tr>
                                    <td colspan="6" class="text-center">{{ \App\CPU\translate('No SEO data found.') }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
<script src="{{ asset('assets/back-end') }}/vendor/datatables/jquery.dataTables.min.js"></script>
<script src="{{ asset('assets/back-end') }}/vendor/datatables/dataTables.bootstrap4.min.js"></script>

<script>
    $(document).ready(function() {
        $('#dataTable').DataTable();
    });
</script>
@endpush
