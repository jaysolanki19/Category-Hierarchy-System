@extends('layouts.app')
@section('title', 'Category List')
@section('content')

    <div class="card mt-5">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Category List</h2>
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal"
                        data-bs-target="#addCategoryModal">
                        <i class="fa-solid fa-plus"></i> Add Category
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <ul class="list-group" id="category-list">
                @foreach ($categories as $category)
                    <li class="list-group-item" data-id="{{ $category->id }}">
                        <div class="d-flex justify-content-between">
                            {{ $category->name }}
                            <div class="button-group d-flex">
                                <a class="btn btn-info btn-sm me-2" href="{{ route('categories.show', $category->id) }}">
                                    <i class="fa-solid fa-list"></i> Show
                                </a>
                                <button type="button" class="btn btn-primary btn-sm me-2" data-bs-toggle="modal"
                                    data-bs-target="#editCategoryModal" data-id="{{ $category->id }}"
                                    data-name="{{ $category->name }}" data-parent-id="{{ $category->parent_id }}">
                                    Edit
                                </button>
                                <button value="{{ $category->id }}" type="button" class="btn btn-danger btn-sm deletebtn">
                                    <i class="fa-solid fa-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                        @if ($category->children)
                            <ul class="list-group mt-2">
                                @include('pages.categories.partials.children', [
                                    'children' => $category->children,
                                ])
                            </ul>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    {{-- add category model start --}}
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCategoryModalLabel">Add New Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="categoryForm" action="{{ route('categories.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label"><strong>Name:</strong></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                id="name" placeholder="Name">
                            @error('name')
                                <div class="form-text text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="parent_id" class="form-label"><strong>Select Parent Category:</strong></label>
                            <select class="form-control" name="parent_id">
                                <option value="">Select Parent Category</option>
                                @foreach ($all_categories as $id => $name)
                                    <option value="{{ $id }}" {{ old('parent_id') == $id ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('parent_id')
                                <div class="form-text text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-success"><i class="fa-solid fa-floppy-disk"></i>
                            Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- add category model end --}}

    {{-- edit category model start --}}
    <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCategoryModalLabel">Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editCategoryForm" action="{{ route('categories.update', ':id') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="name" class="form-label"><strong>Name:</strong></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                id="name" placeholder="Name">
                            @error('name')
                                <div class="form-text text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="parent_id" class="form-label"><strong>Select Parent Category:</strong></label>
                            <select class="form-control" name="parent_id" id="parent_id">
                                <option value="">Select Parent Category</option>
                                @foreach ($all_categories as $id => $name)
                                    <option value="{{ $id }}" {{ old('parent_id') == $id ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('parent_id')
                                <div class="form-text text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-success"><i class="fa-solid fa-floppy-disk"></i>
                            Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- edit category model end --}}

@endsection
@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var el = document.getElementById('category-list');
            var sortable = Sortable.create(el, {
                onEnd: function(evt) {
                    var order = sortable.toArray();
                    $.ajax({
                        url: '{{ route('categories.reorder') }}',
                        type: 'POST',
                        data: {
                            categories: order,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            console.log(response);
                        },
                        error: function(error) {
                            console.error(error);
                        }
                    });
                }
            });
        });
        $(document).ready(function() {
            $("#categoryForm").validate({
                rules: {
                    name: {
                        required: true,
                        minlength: 3
                    }
                },
                messages: {
                    name: {
                        required: "Please enter a category name.",
                        minlength: "Category name must be at least 3 characters."
                    }
                },
                submitHandler: function(form) {
                    form.submit();
                }
            });

            $('#editCategoryModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');
                var name = button.data('name');
                var parentId = button.data('parent-id');

                var modal = $(this);
                modal.find('.modal-body #name').val(name);
                modal.find('.modal-body #parent_id').val(parentId);

                var action = $('#editCategoryForm').attr('action').replace(':id', id);
                $('#editCategoryForm').attr('action', action);
            });

            $('#editCategoryForm').validate({
                rules: {
                    name: {
                        required: true,
                        minlength: 3
                    },
                },
                messages: {
                    name: {
                        required: "Please enter a category name",
                        minlength: "Category name must be at least 3 characters."
                    },
                },
                submitHandler: function(form) {
                    form.submit();
                }
            });

            $(".deletebtn").click(function(e) {
                e.preventDefault();
                var getdelid = $(this).val();
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.value) {
                        var url = "{{ route('categories.destroy', ':id') }}";
                        url = url.replace(':id', getdelid);
                        $.ajax({
                            type: "delete",
                            url: url,
                            dataType: "JSON",
                            data: {
                                "_token": "{{ csrf_token() }}",
                            },
                            success: function(response) {
                                if (response.status == 'success') {
                                    Swal.fire({
                                        icon: response.status,
                                        text: response.message,
                                        toast: true,
                                        position: "top-end",
                                        showConfirmButton: false,
                                        timer: 3000,
                                        timerProgressBar: true,
                                    });
                                    setTimeout(function() {
                                        location.reload();
                                    }, 3000);
                                } else {
                                    Swal.fire({
                                        icon: response.status,
                                        text: response.message,
                                        toast: true,
                                        position: "top-end",
                                        showConfirmButton: false,
                                        timer: 3000,
                                        timerProgressBar: true,
                                    });
                                }
                            },
                            error: function(error) {
                                Swal.fire({
                                    icon: error.responseJSON.status,
                                    title: "Fail!",
                                    text: error.responseJSON.message,
                                    showConfirmButton: true,
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
