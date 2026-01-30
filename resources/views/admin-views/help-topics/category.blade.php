@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('Category'))

@section('content')
    <a href="{{ route('admin.helpTopic.subcategory') }}" class="btn btn--primary btn-icon-split for-addFaq">
        <i class="tio-add"></i>
        <span class="text">{{ \App\CPU\translate('Add') }} {{ \App\CPU\translate('faq') }}
            {{ \App\CPU\translate('Subategory') }} </span>
    </a>
    <div class="row">
        <div class="col-md-12">
            <!-- Add Category -->
            <div class="card">
                <div class="card-body text-{{ Session::get('direction') === 'rtl' ? 'right' : 'left' }}">
                    <form action="{{ route('admin.helpTopic.category.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>{{ \App\CPU\translate('Category Name') }}</label>
                            <input type="text" name="name" class="form-control"
                                placeholder="{{ \App\CPU\translate('Category Name') }}" required>
                        </div>
                        <button type="submit" class="btn btn-primary">{{ \App\CPU\translate('Save') }}</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Category List -->
        <div class="col-md-12 mt-3">
            <div class="card">
                <div class="card-header">
                    <h5>{{ \App\CPU\translate('Category List') }}</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>{{ \App\CPU\translate('SL') }}</th>
                                <th>{{ \App\CPU\translate('Category Name') }}</th>
                                <th>{{ \App\CPU\translate('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $key => $category)
                                <tr id="data-{{ $category->id }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="category-name">{{ $category->name }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary edit-category" data-id="{{ $category->id }}"
                                            data-name="{{ $category->name }}">
                                            <i class="tio-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger delete-category" data-id="{{ $category->id }}"
                                            data-toggle="tooltip" data-placement="top"
                                            title="{{ \App\CPU\translate('Delete') }}">
                                            <i class="tio-delete"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Edit Modal (static) -->
                    <div class="modal fade" id="editCategoryModal" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <form method="POST" id="editForm">
                                @csrf
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">{{ \App\CPU\translate('Edit Category') }}</h5>
                                        <button type="button" class="close" data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="id" id="edit_category_id">
                                        <div class="form-group">
                                            <label>{{ \App\CPU\translate('Category Name') }}</label>
                                            <input type="text" name="name" id="edit_category_name"
                                                class="form-control" required>
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
            // Delete category
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
                        $.post("{{ route('admin.helpTopic.category.delete') }}", {
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

            // Open edit modal and fill form
            $('.edit-category').click(function() {
                let id = $(this).data('id');
                let name = $(this).data('name');

                $('#edit_category_id').val(id);
                $('#edit_category_name').val(name);

                $('#editForm').attr('action', '{{ url(' / ') }}/admin/helpTopic/category/update/' + id);
                $('#editCategoryModal').modal('show');
            });
        });
    </script>
@endpush
