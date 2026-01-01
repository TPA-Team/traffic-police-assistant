@extends('admin.layouts.app')

@section('title', 'Create Violation Type')

@section('content')

<div class="container mt-4">

    <div class="d-flex justify-content-between mb-3">
        <h3>Create Violation Type</h3>
        <a href="{{ route('admin.violationTypes.index') }}" class="btn btn-secondary">
            ← Back
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Error!</strong> Please fix the issues below.
            <ul class="mt-2 mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <div class="card shadow p-4">
        <form action="{{ route('admin.violationTypes.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">Violation Name</label>
                <input type="text" name="name" class="form-control"
                       value="{{ old('name') }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Description (Optional)</label>
                <textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Fine Amount</label>
                <input type="number" min="0" name="fine_amount" class="form-control"
                       value="{{ old('fine_amount') }}" required>
            </div>

            <button type="submit" class="btn btn-primary px-4">
                Save Violation Type
            </button>
        </form>
    </div>

</div>

@endsection
