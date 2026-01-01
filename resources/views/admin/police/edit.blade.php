@extends('admin.layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="container mt-5" style="max-width: 600px;">
    <h3 class="mb-4 text-center">Edit User</h3>

    {{-- زر الرجوع --}}
    <div class="mb-3">
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left-circle"></i> Back to Users
        </a>
    </div>

    {{-- عرض الأخطاء --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.users.saveupdate', $user->id) }}" method="POST">
        @csrf
        @method('PATCH')

        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" placeholder="9 digits">
        </div>

   

               {{-- Status --}}
                    <div class="col-md-6">
                        <label class="form-label">Account Status</label>
                        <select name="is_active" class="form-select form-select-lg" required>
                            <option value="1" {{ old('is_active') == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

        <button type="submit" class="btn btn-primary w-100">Save Changes</button>
    </form>
</div>
@endsection