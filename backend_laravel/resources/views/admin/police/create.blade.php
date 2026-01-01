@extends('admin.layout')

@section('content')

<style>
    .modern-card {
        border-radius: 14px !important;
        border: none;
        overflow: hidden;
        background: #ffffff;
        box-shadow: 0 4px 18px rgba(0,0,0,0.08);
    }

    .modern-header {
        background: linear-gradient(135deg, #0d6efd, #0056d6);
        padding: 20px 25px;
        color: white;
        border-bottom: none;
    }

    .modern-header h4 {
        margin: 0;
        font-weight: 600;
        letter-spacing: .5px;
    }

    .modern-input,
    .modern-select {
        border-radius: 10px !important;
        border: 1.3px solid #d5d5d5;
        padding: 12px 14px;
        font-size: 16px;
        transition: .25s;
    }

    .modern-input:focus,
    .modern-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 3px rgba(13,110,253,0.15);
    }

    .modern-label {
        font-weight: 600;
        margin-bottom: 6px;
        color: #333;
    }

    .submit-btn {
        border-radius: 10px;
        font-size: 18px;
        padding: 12px 28px;
        background: linear-gradient(135deg, #0d6efd, #0056d6);
        border: none;
        transition: .3s;
    }

    .submit-btn:hover {
        background: linear-gradient(135deg, #0056d6, #003c99);
        transform: translateY(-2px);
    }

    .alert-danger {
        border-radius: 10px;
    }
</style>


<div class="container py-4">
    <div class="card modern-card">

        <div class="modern-header">
            <h4>Create New Police Officer</h4>
        </div>

        <div class="card-body p-4">

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="alert alert-danger px-3 py-2">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf

                <div class="row mb-3">

                    {{-- Name --}}
                    <div class="col-md-6">
                        <label class="modern-label">Name</label>
                        <input type="text" name="name" 
                            class="form-control modern-input"
                            value="{{ old('name') }}" required>
                    </div>

                    {{-- Email --}}
                    <div class="col-md-6">
                        <label class="modern-label">Email</label>
                        <input type="email" name="email" 
                            class="form-control modern-input"
                            value="{{ old('email') }}" required>
                    </div>

                </div>

                <div class="row mb-3">

                    {{-- Phone --}}
                    <div class="col-md-6">
                        <label class="modern-label">Phone (Optional)</label>
                        <input type="text" name="phone"
                            class="form-control modern-input"
                            value="{{ old('phone') }}">
                    </div>

                    {{-- Password --}}
                    <div class="col-md-6">
                        <label class="modern-label">Password</label>
                        <input type="password" name="password" 
                            class="form-control modern-input"
                            required>
                    </div>

                </div>

                <div class="row mb-3">

                    {{-- Role --}}
                    <div class="col-md-6">
                        <label class="modern-label">User Role</label>
                        <select name="role" 
                            class="form-select modern-select" required>
                            <option value="Police_officer" 
                                {{ old('role') == 'Police_officer' ? 'selected' : '' }}>
                                Police Officer
                            </option>
                            <option value="Police_manager" 
                                {{ old('role') == 'Police_manager' ? 'selected' : '' }}>
                                Police Manager
                            </option>
                        </select>
                    </div>

                    {{-- Status --}}
                    <div class="col-md-6">
                        <label class="modern-label">Account Status</label>
                        <select name="is_active" 
                            class="form-select modern-select" required>
                            <option value="1" {{ old('is_active') == '1' ? 'selected' : '' }}>
                                Active
                            </option>
                            <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>
                                Inactive
                            </option>
                        </select>
                    </div>

                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary submit-btn">
                        Create User
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

@endsection
