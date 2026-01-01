@extends('admin.layouts.app')

@section('title', 'Violation Types')

@section('content')

<div class="container mt-4">

    <div class="d-flex justify-content-between mb-3">
        <h3>Violation Types</h3>
        <a href="{{ route('admin.violationTypes.create') }}" class="btn btn-primary">
            + Add New Violation Type
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Description</th>
            <th>Fine Amount</th>
            <th>Created At</th>
        </tr>
        </thead>

        <tbody>
        @foreach ($violationTypes as $violation)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $violation->name }}</td>
                <td>{{ $violation->description }}</td>
                <td>{{ $violation->fine_amount }}</td>
                <td>{{ $violation->created_at->format('Y-m-d') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

</div>

@endsection
