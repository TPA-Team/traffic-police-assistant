@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')

<style>
    /* ===== Modern Card ===== */
    .stat-card {
        border-radius: 16px !important;
        padding: 22px 10px;
        color: #fff;
        box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        transition: .3s;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    }

    .stat-title {
        font-size: 15px;
        opacity: .9;
        font-weight: 500;
    }

    .stat-number {
        font-size: 32px;
        font-weight: 700;
        margin: 5px 0 0 0;
    }

    /* Colors */
    .bg-gradient-primary {
        background: linear-gradient(135deg, #0062ff, #003fbb);
    }

    .bg-gradient-success {
        background: linear-gradient(135deg, #28a745, #1f7a34);
    }

    .bg-gradient-danger {
        background: linear-gradient(135deg, #dc3545, #9b1f28);
    }

    /* ===== Table Styling ===== */
    .table-modern thead th {
        background: #f8f9fc !important;
        font-weight: 600;
        padding: 12px;
    }

    .table-modern tbody tr {
        transition: .25s;
    }

    .table-modern tbody tr:hover {
        background: #f4f8ff;
    }

    .badge {
        font-size: 13px;
        padding: 6px 10px;
        border-radius: 6px;
    }

    /* ===== Chart Container ===== */
    .chart-box {
        width: 260px;
        margin: auto;
        padding-top: 10px;
    }
</style>


<div class="container-fluid">

   {{-- =========================
    🔹 Chart + Status Cards (Side by Side)
========================== --}}
<div class="row mb-4 align-items-center">

    {{-- Chart --}}
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-light fw-bold">Users Status Chart</div>
            <div class="card-body text-center">
                <div style="max-width: 260px; margin: auto;">
                    <canvas id="usersChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Active / Inactive Cards --}}
    <div class="col-md-6">
        <div class="row">

          <div class="col-md-12 mb-3">
            <div class="stat-card bg-gradient-primary">
                <div class="stat-title">Total Users</div>
                <div class="stat-number">{{ $stats['totalUsers'] }}</div>
            </div>
        </div>

            <div class="col-md-12 mb-3">
                <div class="stat-card bg-gradient-success">
                    <div class="stat-title">Active Users</div>
                    <div class="stat-number">{{ $stats['activeUsers'] }}</div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="stat-card bg-gradient-danger">
                    <div class="stat-title">Inactive Users</div>
                    <div class="stat-number">{{ $stats['inactiveUsers'] }}</div>
                </div>
            </div>

            

        </div>
    </div>

</div>


    {{-- =========================
        🔹 Latest 5 Users Table
    ========================== --}}
    <div class="card shadow-sm mb-4 modern-card">
        <div class="card-header bg-light fw-bold">
            Last 5 Added Users
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-modern table-hover mb-0 text-center align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($stats['latestUsers'] as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td class="text-capitalize">{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->phone ?? '-' }}</td>
                                <td>{{ ucwords(str_replace('_',' ', $user->role)) }}</td>

                                <td>
                                    @if($user->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-muted py-3">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>
    </div>

</div>
@endsection


{{-- =========================
    🔹 Chart Script
========================== --}}
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('usersChart');

    if (ctx) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Active', 'Inactive'],
                datasets: [{
                    data: [{{ $stats['activeUsers'] }}, {{ $stats['inactiveUsers'] }}],
                    backgroundColor: ['#28a745', '#dc3545'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                cutout: '70%',
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }
});
</script>
@endsection
