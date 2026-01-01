<aside class="main-sidebar sidebar-dashboard elevation-4 d-flex flex-column bg-dashboard">
    <div class="p-3 d-flex flex-column flex-grow-1">

        <!-- Top Title -->
        <div class="text-center mb-4">
            <h5 class="mb-0 text-white fw-bold">Admin Dashboard</h5>
        </div>

        <!-- Navigation Menu -->
        <nav class="nav flex-column flex-grow-1 mt-2">

            <!-- Dashboard -->
            <a class="nav-link mb-2 {{ request()->routeIs('admin.home') ? 'active' : '' }}" 
               href="{{ route('admin.home') }}">
                <i class="bi bi-house-door me-2"></i> Dashboard
            </a>

            <!-- Police Accounts -->
            <a class="nav-link mb-2 {{ request()->routeIs('admin.users*') ? 'active' : '' }}" 
               href="{{ route('admin.users.index') }}">
                <i class="bi bi-person-badge me-2"></i> Police Accounts
            </a>

            <!-- Violations Section Title -->
            <div class="nav-section-title text-white-50 mt-3 mb-1 ms-1" style="font-size: 0.75rem;">
                Violations Management
            </div>

            <!-- Violation Types List -->
            <a class="nav-link mb-2 {{ request()->routeIs('admin.violationTypes.index') ? 'active' : '' }}"
               href="{{ route('admin.violationTypes.index') }}">
                <i class="bi bi-exclamation-triangle me-2"></i> Violation Types
            </a>

           

        </nav>

        <!-- Logout Button -->
        <div class="mt-auto pt-3">
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-logout w-100 text-center">
                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                </button>
            </form>
        </div>

    </div>
</aside>

<style>
/* Sidebar Colors & Layout */
.bg-dashboard {
    background: linear-gradient(180deg, #4e73df, #2e59d9);
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    color: #fff;
    padding-top: 1rem;
}

/* Navigation Section Title */
.nav-section-title {
    letter-spacing: 1px;
    text-transform: uppercase;
}

/* Navigation Links */
.nav-link {
    color: #e2e6ea;
    transition: all 0.3s ease;
    border-radius: 0.5rem;
    padding: 0.65rem 1rem;
    display: flex;
    align-items: center;
    font-weight: 500;
}
.nav-link i {
    font-size: 1.2rem;
}
.nav-link:hover {
    background: rgba(255,255,255,0.15);
    color: #fff;
}
.nav-link.active {
    background: #1b4b91;
    color: #fff;
    font-weight: bold;
}

/* Logout Button */
.btn-logout {
    background-color: #f8f9fc; 
    color: #4e73df; 
    font-weight: 500;
    border-radius: 0.5rem;
    padding: 0.5rem 1rem;
    transition: all 0.3s ease;
}
.btn-logout:hover {
    background-color: #e2e6ea;
    color: #2e59d9;
}
</style>
