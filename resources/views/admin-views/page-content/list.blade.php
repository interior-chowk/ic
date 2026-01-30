@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('Terms & Condition'))

@push('css_or_js')
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                <img width="20" src="{{ asset('/public/assets/back-end/img/Pages.png') }}" alt="">
                {{ \App\CPU\translate('pages') }}
            </h2>
        </div>
        <!-- End Page Title -->

        <!-- Inlile Menu -->
        @include('admin-views.business-settings.pages-inline-menu')
        <!-- End Inlile Menu -->

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ \App\CPU\translate('page content') }}</h5>
                    </div>

                    <form action="{{ route('admin.page-content.store') }}" method="post">
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <input type="text" class="form-control" name="page"></textarea>
                            </div>
                            <div class="form-group">
                                <textarea class="form-control" id="editor" name="content"></textarea>
                            </div>
                            <div class="form-group">
                                <input class="form-control btn--primary" type="submit"
                                    value="{{ \App\CPU\translate('submit') }}" name="btn">
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ \App\CPU\translate('page content') }}</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">{{ \App\CPU\translate('SL') }}</th>
                                    <th scope="col">{{ \App\CPU\translate('page') }}</th>
                                    <th scope="col">{{ \App\CPU\translate('content') }}</th>
                                    <th scope="col">{{ \App\CPU\translate('action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($page_content as $key => $data)
                                    <tr>
                                        <th scope="row">{{ $key + 1 }}</th>
                                        <td>{{ $data['page'] }}</td>
                                        <td>{!! $data['content'] !!}</td>
                                        <td>
                                            <a class="btn btn--primary btn-sm editBtn" href="javascript:void(0);"
                                                data-id="{{ $data['id'] }}" data-page="{{ $data['page'] }}"
                                                data-content="{{ htmlspecialchars($data['content']) }}">
                                                {{ \App\CPU\translate('edit') }}
                                            </a>
                                            <a class="btn btn--danger btn-sm"
                                                href="{{ route('admin.page-content.delete', [$data['id']]) }}">
                                                {{ \App\CPU\translate('delete') }}
                                            </a>

                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    {{-- ck editor --}}
    <script src="{{ asset('/') }}vendor/ckeditor/ckeditor/ckeditor.js"></script>
    <script src="{{ asset('/') }}vendor/ckeditor/ckeditor/adapters/jquery.js"></script>
    <script>
        $('#editor').ckeditor({
            contentsLangDirection: '{{ Session::get('direction') }}',
        });
    </script>
    {{-- ck editor --}}
    <script>
        $(document).ready(function() {
            // Initialize a second CKEditor for modal textarea
            let modalEditor;
            setTimeout(() => {
                modalEditor = CKEDITOR.replace('edit_content', {
                    contentsLangDirection: '{{ Session::get('direction') }}',
                });
            }, 300);

            $('.editBtn').click(function() {
                let id = $(this).data('id');
                let page = $(this).data('page');
                let content = $(this).data('content');

                $('#edit_page_id').val(id);
                $('#edit_page').val(page);

                if (modalEditor) {
                    modalEditor.setData(content);
                }

                $('#editPageModal').modal('show');
            });
        });
    </script>
    <!-- Edit Modal -->
    <div class="modal fade" id="editPageModal" tabindex="-1" aria-labelledby="editPageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="editPageForm" method="POST" action="{{ route('admin.page-content.update') }}">
                    @csrf
                    <input type="hidden" name="id" id="edit_page_id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editPageModalLabel">{{ \App\CPU\translate('Edit Page Content') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>{{ \App\CPU\translate('Page') }}</label>
                            <input type="text" class="form-control" name="page" id="edit_page">
                        </div>
                        <div class="form-group mt-2">
                            <label>{{ \App\CPU\translate('Content') }}</label>
                            <textarea class="form-control" name="content" id="edit_content"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary">{{ \App\CPU\translate('Update') }}</button>
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ \App\CPU\translate('Close') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endpush
