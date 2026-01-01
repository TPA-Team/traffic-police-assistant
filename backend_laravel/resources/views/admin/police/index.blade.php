@extends('admin.layout')

@section('title', 'Manage Police Accounts')

@section('content')

<style>
    /* ===== Header ===== */
    .app-content-header {
        margin-bottom: 25px;
    }

    .app-title {
        font-size: 28px;
        font-weight: 700;
        color: #2b2b2b;
    }

    /* ===== Card Style ===== */
    .modern-card {
        border-radius: 14px !important;
        border: none;
        background: #fff;
        box-shadow: 0 4px 14px rgba(0,0,0,0.07);
    }

    /* ===== Table Style ===== */
    .modern-table th {
        background-color: #f8f9fc !important;
        font-weight: 600;
        border-bottom: 2px solid #e5e7eb;
    }

    .modern-table td {
        vertical-align: middle;
        font-size: 15px;
        padding: 12px 10px;
    }

    .modern-table tbody tr {
        transition: .25s;
    }

    .modern-table tbody tr:hover {
        background: #f4f8ff;
        cursor: pointer;
    }

    /* ===== Buttons ===== */
    .btn-modern {
        border-radius: 10px !important;
        font-weight: 500;
        padding: 8px 18px;
        transition: .25s;
    }

    .btn-create {
        background: linear-gradient(135deg, #0d6efd, #0056d6);
        color: #fff;
        border: none;
    }

    .btn-create:hover {
        background: linear-gradient(135deg, #0056d6, #003c99);
        transform: translateY(-2px);
    }

    .btn-action {
        border-radius: 8px !important;
        font-size: 14px;
        padding: 6px 14px;
    }

    .badge {
        font-size: 13px;
        padding: 6px 10px;
        border-radius: 6px;
    }

</style>


<div class="app-content-header">
    <div class="container-fluid">
        <div class="row align-items-center justify-content-between">

            <div class="col-md-6">
                <h3 class="app-title">Police Accounts</h3>
            </div>

            <div class="col-md-6 text-md-end text-center">
                <a href="{{ route('admin.users.create') }}" class="btn btn-modern btn-create shadow-sm">
                    <i class="bi bi-plus-circle me-1"></i> Create New Account
                </a>
            </div>

        </div>
    </div>
</div>


<div class="app-content">
    <div class="container-fluid">

        <div class="card modern-card">
            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-bordered table-hover text-center modern-table mb-0">

                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th width="25%">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($users as $p)
                            <tr>
                                <td>{{ $p->id }}</td>

                                <td class="text-capitalize">{{ $p->name }}</td>

                                <td>{{ $p->email }}</td>

                                <td>{{ $p->Phone ? $p->Phone : '—' }}</td>

                                <td class="text-capitalize">
                                    {{ str_replace('_', ' ', $p->role) }}
                                </td>

                                <td>
                                    @if($p->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>

                                <td>
                                    <div class="d-flex justify-content-center gap-2">

                                        {{-- Edit --}}
                                        <a href="{{ route('admin.users.edit', $p->id) }}"
                                            class="btn btn-warning btn-action">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>

                                        {{-- Toggle --}}
                                        <form action="{{ route('admin.users.toggle', $p->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="btn btn-outline-{{ $p->is_active ? 'danger' : 'success' }} btn-action">
                                                <i class="bi bi-{{ $p->is_active ? 'slash-circle' : 'check-circle' }}"></i>
                                                {{ $p->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>

                                        {{-- Delete --}}
                                        <form action="{{ route('admin.users.delete', $p->id) }}" method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this account?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-action">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>

                            @empty
                            <tr>
                                <td colspan="7" class="text-muted py-3">No police accounts found.</td>
                            </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>

            </div>
        </div>

    </div>
</div>

@endsection
