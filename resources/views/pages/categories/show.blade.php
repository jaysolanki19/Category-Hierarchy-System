@extends('layouts.app')

@section('content')
    <div class="card mt-5">
        <h2 class="card-header">Show Category</h2>
        <div class="card-body">
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a class="btn btn-primary btn-sm" href="{{ route('categories.index') }}">
                    <i class="fa fa-arrow-left"></i> Back
                </a>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Name:</strong> <br />
                        {{ $category->name }}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12 mt-2">
                    <div class="form-group">
                        <strong>Parent Category:</strong> <br />
                        @if ($category->parent)
                            {{ $category->parent->name }}
                        @else
                            None
                        @endif
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12 mt-2">
                    <div class="form-group">
                        <strong>Child Categories:</strong> <br />
                        @if ($category->children->isNotEmpty())
                            <ul>
                                @foreach ($category->children as $child)
                                    <li>{{ $child->name }}</li>
                                @endforeach
                            </ul>
                        @else
                            None
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
