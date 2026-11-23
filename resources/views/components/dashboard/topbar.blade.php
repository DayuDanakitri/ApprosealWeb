
<header class="topbar">

    <button class="sidebar-toggle" id="sidebarToggle" type="button">
        <i class="bi bi-list"></i>
    </button>

    <div class="topbar-left">
        <span class="network-status">
            <span class="status-dot"></span>
            Local Development
        </span>

        <span class="wallet-status">
            <i class="bi bi-wallet2"></i>
            Wallet not connected
        </span>
    </div>

    <div class="topbar-right">

        <div class="role-display">
            <i class="bi bi-person-workspace"></i>
            {{ auth()->user()->role->name }}
        </div>

        <div class="user-dropdown dropdown">

            <button
                class="user-avatar"
                type="button"
                data-bs-toggle="dropdown"
                aria-expanded="false"
            >
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </button>

            <div class="dropdown-menu dropdown-menu-end user-menu">

                <div class="user-menu-info">
                    <strong>{{ auth()->user()->name }}</strong>
                    <small>{{ auth()->user()->email }}</small>
                    <small>{{ auth()->user()->role->name }}</small>
                </div>

                <div class="dropdown-divider"></div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button type="submit" class="dropdown-item text-danger">
                        <i class="bi bi-box-arrow-right me-2"></i>
                        Sign Out
                    </button>
                </form>

            </div>

        </div>

    </div>

</header>
