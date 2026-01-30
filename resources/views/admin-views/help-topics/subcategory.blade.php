@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('Category'))

@section('content')
    <div class="row">
        <div class="col-md-12">
            <!-- Add Subcategory -->
            <div class="card">
                <div class="card-body text-{{ Session::get('direction') === 'rtl' ? 'right' : 'left' }}">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{ route('admin.helpTopic.subcategory.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>{{ \App\CPU\translate('Category') }}</label>
                            <select class="form-control" name="category_id" required>
                                <option value="">{{ \App\CPU\translate('Select Category') }}</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>{{ \App\CPU\translate('Subcategory Name') }}</label>
                            <input type="text" name="sub_cat_name" class="form-control"
                                placeholder="{{ \App\CPU\translate('Subcategory Name') }}" required>
                        </div>
                        <div class="form-group">
                            <label>{{ \App\CPU\translate('Link') }}</label>
                            <input type="text" name="link" class="form-control"
                                placeholder="{{ \App\CPU\translate('Link') }}">
                        </div>
                        <div class="form-group">
                            <label>{{ \App\CPU\translate('Link Name') }}</label>
                            <input type="text" name="link_name" class="form-control"
                                placeholder="{{ \App\CPU\translate('Link Name') }}">
                        </div>
                        <div class="form-group">
                            <label>{{ \App\CPU\translate('Link Short Description') }}</label>
                            <input type="text" name="link_short_description" class="form-control"
                                placeholder="{{ \App\CPU\translate('Link Short Description') }}">
                        </div>


                        <button type="submit" class="btn btn-primary">{{ \App\CPU\translate('Save') }}</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Subcategory List -->
        <div class="col-md-12 mt-3">
            <div class="card">
                <div class="card-header">
                    <h5>{{ \App\CPU\translate('Subcategory List') }}</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>{{ \App\CPU\translate('SL') }}</th>
                                <th>{{ \App\CPU\translate('Category Name') }}</th>
                                <th>{{ \App\CPU\translate('Subcategory Name') }}</th>
                                <th>{{ \App\CPU\translate('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($subcategories as $key => $subcategory)
                                <tr id="data-{{ $subcategory->id }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $subcategory->category->name ?? '' }}</td>
                                    <td>{{ $subcategory->sub_cat_name }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary edit-category"
                                            data-id="{{ $subcategory->id }}" data-cat_id="{{ $subcategory->cat_id }}"
                                            data-sub_cat_name="{{ $subcategory->sub_cat_name }}">
                                            <i class="tio-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger delete-category"
                                            data-id="{{ $subcategory->id }}" data-toggle="tooltip" data-placement="top"
                                            title="{{ \App\CPU\translate('Delete') }}">
                                            <i class="tio-delete"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editCategoryModal" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <form method="POST" id="editForm">
                                @csrf
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">{{ \App\CPU\translate('Edit Subcategory') }}</h5>
                                        <button type="button" class="close" data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="id" id="edit_category_id">
                                        <div class="form-group">
                                            <label>{{ \App\CPU\translate('Category') }}</label>
                                            <select class="form-control" name="category_id" id="edit_category_select">
                                                @foreach ($categories as $cat)
                                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>{{ \App\CPU\translate('Subcategory Name') }}</label>
                                            <input type="text" name="sub_cat_name" id="edit_category_name"
                                                class="form-control" required>
                                        </div>
                                        <div class="form-group">
                                            <label>{{ \App\CPU\translate('Link') }}</label>
                                            <input type="text" class="form-control" name="link"
                                                placeholder="{{ \App\CPU\translate('Type Link') }}" id="e_link">
                                        </div>
                                        <div class="form-group">
                                            <label>{{ \App\CPU\translate('Link Name') }}</label>
                                            <input type="text" class="form-control" name="link_name"
                                                placeholder="{{ \App\CPU\translate('Type Link Name') }}" id="e_link_name">
                                        </div>
                                        <div class="form-group">
                                            <label>{{ \App\CPU\translate('Link Short Description') }}</label>
                                            <input type="text" class="form-control" name="link_short_description"
                                                placeholder="{{ \App\CPU\translate('Type Link Short Description') }}"
                                                id="e_link_short_description">
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">{{ \App\CPU\translate('Close') }}</button>
                                        <button type="submit"
                                            class="btn btn-primary">{{ \App\CPU\translate('Update') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- End Modal -->
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            // Delete subcategory
            $('.delete-category').click(function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: '{{ \App\CPU\translate('Are you sure?') }}',
                    text: '{{ \App\CPU\translate('This action cannot be undone!') }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '{{ \App\CPU\translate('Yes, delete it!') }}'
                }).then((result) => {
                    console.log(result);

                    if (result) {
                        $.post("{{ route('admin.helpTopic.subcategory.delete') }}", {
                            _token: '{{ csrf_token() }}',
                            id: id
                        }, function() {
                            toastr.success(
                                '{{ \App\CPU\translate('Deleted successfully') }}');
                            $('#data-' + id).fadeOut();
                        });
                    }
                });
            });

            // Edit modal populate
            $('.edit-category').click(function() {
                let id = $(this).data('id');
                let cat_id = $(this).data('cat_id');
                let sub_cat_name = $(this).data('sub_cat_name');
                let link = $(this).data('link');
                let link_name = $(this).data('link_name');
                let link_short_description = $(this).data('link_short_description');

                $('#edit_category_id').val(id);
                $('#edit_category_name').val(sub_cat_name);
                $('#edit_category_select').val(cat_id);
                $('#edit_link').val(link);
                $('#edit_link_name').val(link_name);
                $('#edit_link_short_description').val(link_short_description);

                $('#editForm').attr('action', '{{ url('admin/helpTopic/subcategory/update') }}/' + id);
                $('#editCategoryModal').modal('show');
            });
        });
    </script>
@endpush
