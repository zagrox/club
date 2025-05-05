@extends('layouts.app')

@section('title', 'Edit Payment Option')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Payment Options /</span> Edit Option
    </h4>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <h5 class="card-header">Edit Payment Option</h5>
                <div class="card-body">
                    <form action="{{ route('payment-options.update', $id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Option Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', 'Sample Option') }}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', 'Sample option description') }}</textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="active" selected>Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary me-2">Update</button>
                            <a href="{{ route('payment-options.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 