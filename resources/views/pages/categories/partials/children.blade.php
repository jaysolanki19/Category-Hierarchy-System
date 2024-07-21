@foreach ($children as $child)
    <li class="list-group-item">
        <div class="d-flex justify-content-between">
            {{ $child->name }}
            <div class="button-group d-flex">
                <a class="btn btn-info btn-sm me-2" href="{{ route('categories.show', $child->id) }}">
                    <i class="fa-solid fa-list"></i> Show
                </a>
                <button type="button" class="btn btn-primary btn-sm me-2" data-bs-toggle="modal"
                    data-bs-target="#editCategoryModal" data-id="{{ $child->id }}"
                    data-name="{{ $child->name }}" data-parent-id="{{ $child->parent_id }}">
                    Edit
                </button>
                <button value="{{ $child->id }}" type="button" class="btn btn-danger btn-sm deletebtn">
                    <i class="fa-solid fa-trash"></i> Delete
                </button>
            </div>
        </div>
        @if ($child->children)
            <ul class="list-group mt-2">
                @include('pages.categories.partials.children', ['children' => $child->children])
            </ul>
        @endif
    </li>
@endforeach
