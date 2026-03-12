<nav class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-brand">
            @if(\App\Models\WebsiteSetting::get('logo'))
                <img src="{{ asset('storage/'.\App\Models\WebsiteSetting::get('logo')) }}" alt="Logo" class="brand-logo">
            @else
                <div class="brand-icon"><i class="bi bi-shop"></i></div>
            @endif
            <span class="brand-name">{{ \App\Models\WebsiteSetting::get('restaurant_name','Restaurant') }}</span>
        </div>
        <button class="sidebar-toggle" id="sidebarToggle">
            <i class="bi bi-list"></i>
        </button>
    </div>

    <div class="sidebar-menu">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i><span>Dashboard</span>
                </a>
            </li>

            <li class="nav-section-title"><span>Menu</span></li>
            <li class="nav-item">
                <a href="{{ route('admin.menu-categories.index') }}" class="nav-link {{ request()->routeIs('admin.menu-categories.*') ? 'active' : '' }}">
                    <i class="bi bi-grid-3x3-gap"></i><span>Menu Categories</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.menu-items.index') }}" class="nav-link {{ request()->routeIs('admin.menu-items.*') ? 'active' : '' }}">
                    <i class="bi bi-egg-fried"></i><span>Menu Items</span>
                </a>
            </li>

            <li class="nav-section-title"><span>Operations</span></li>
            <li class="nav-item">
                <a href="{{ route('admin.tables.index') }}" class="nav-link {{ request()->routeIs('admin.tables.*') ? 'active' : '' }}">
                    <i class="bi bi-layout-text-window"></i><span>Tables</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.time-slots.index') }}" class="nav-link {{ request()->routeIs('admin.time-slots.*') ? 'active' : '' }}">
                    <i class="bi bi-clock"></i><span>Time Slots</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.pricing-rules.index') }}" class="nav-link {{ request()->routeIs('admin.pricing-rules.*') ? 'active' : '' }}">
                    <i class="bi bi-currency-rupee"></i><span>Pricing Rules</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.bookings.index') }}" class="nav-link {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}">
                    <i class="bi bi-calendar-check"></i><span>Bookings</span>
                    @php $pendingBookings = \App\Models\Booking::where('status','pending')->count(); @endphp
                    @if($pendingBookings > 0)
                        <span class="badge bg-danger ms-auto">{{ $pendingBookings }}</span>
                    @endif
                </a>
            </li>

            <li class="nav-section-title"><span>Promotions</span></li>
            <li class="nav-item">
                <a href="{{ route('admin.deals-bundles.index') }}" class="nav-link {{ request()->routeIs('admin.deals-bundles.*') ? 'active' : '' }}">
                    <i class="bi bi-tag"></i><span>Deals & Bundles</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.vouchers.index') }}" class="nav-link {{ request()->routeIs('admin.vouchers.*') ? 'active' : '' }}">
                    <i class="bi bi-ticket-perforated"></i><span>Vouchers</span>
                </a>
            </li>

            <li class="nav-section-title"><span>Content</span></li>
            <li class="nav-item">
                <a href="{{ route('admin.blogs.index') }}" class="nav-link {{ request()->routeIs('admin.blogs.*') ? 'active' : '' }}">
                    <i class="bi bi-pencil-square"></i><span>Blog Posts</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.events-highlights.index') }}" class="nav-link {{ request()->routeIs('admin.events-highlights.*') ? 'active' : '' }}">
                    <i class="bi bi-stars"></i><span>Events & Highlights</span>
                </a>
            </li>

            <li class="nav-section-title"><span>Users & Enquiries</span></li>
            <li class="nav-item">
                <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i><span>Users</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.enquiries.index') }}" class="nav-link {{ request()->routeIs('admin.enquiries.*') ? 'active' : '' }}">
                    <i class="bi bi-envelope"></i><span>Enquiries</span>
                    @php $newEnquiries = \App\Models\Enquiry::where('status','new')->count(); @endphp
                    @if($newEnquiries > 0)
                        <span class="badge bg-warning text-dark ms-auto">{{ $newEnquiries }}</span>
                    @endif
                </a>
            </li>

            <li class="nav-section-title"><span>Communications</span></li>
            <li class="nav-item">
                <a href="{{ route('admin.notifications.index') }}" class="nav-link {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}">
                    <i class="bi bi-megaphone"></i><span>Broadcast</span>
                </a>
            </li>

            <li class="nav-section-title"><span>Monitoring</span></li>
            <li class="nav-item">
                <a href="{{ route('admin.activity-logs.index') }}" class="nav-link {{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}">
                    <i class="bi bi-clipboard-data"></i><span>Activity Logs</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.system-logs.index') }}" class="nav-link {{ request()->routeIs('admin.system-logs.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-text"></i><span>System Logs</span>
                </a>
            </li>

            <li class="nav-section-title"><span>System</span></li>
            <li class="nav-item">
                <a href="{{ route('admin.profile.edit') }}" class="nav-link {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}">
                    <i class="bi bi-person-circle"></i><span>My Profile</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <i class="bi bi-gear"></i><span>Settings</span>
                </a>
            </li>

            <li class="nav-item mt-2">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="nav-link w-100 text-start border-0 bg-transparent text-danger">
                        <i class="bi bi-box-arrow-left"></i><span>Logout</span>
                    </button>
                </form>
            </li>
        </ul>
    </div>
</nav>
