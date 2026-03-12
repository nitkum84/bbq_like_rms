<header class="topbar">
    <div class="d-flex align-items-center gap-3">
        <button class="btn btn-link topbar-toggle d-lg-none" id="mobileSidebarToggle">
            <i class="bi bi-list fs-4"></i>
        </button>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                @yield('breadcrumb')
            </ol>
        </nav>
    </div>
    <div class="topbar-right d-flex align-items-center gap-3">
        <div class="topbar-date text-muted small d-none d-md-block">
            <i class="bi bi-calendar3 me-1"></i>{{ now()->format('D, d M Y') }}
        </div>
        <div class="dropdown">
            <button class="btn btn-link topbar-user dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                <img src="{{ auth()->user()->profile_image_url }}" alt="Admin" class="avatar-sm rounded-circle">
                <span class="d-none d-md-inline fw-semibold">{{ auth()->user()->name }}</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow">
                <li><span class="dropdown-item-text small text-muted">{{ auth()->user()->email }}</span></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a href="{{ route('admin.profile.edit') }}" class="dropdown-item">
                        <i class="bi bi-person-circle me-2"></i>My Profile
                    </a>
                </li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="dropdown-item text-danger" type="submit">
                            <i class="bi bi-box-arrow-left me-2"></i>Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>
