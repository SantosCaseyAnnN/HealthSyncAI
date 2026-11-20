<div
    class="mobile-navigation-backdrop"
    x-cloak
    x-show="mobileNavigationOpen"
    x-transition.opacity
    @click="mobileNavigationOpen = false"
    aria-hidden="true"
></div>

<aside
    id="clinical-navigation"
    class="sidebar"
    :class="{ 'mobile-open': mobileNavigationOpen }"
    aria-label="Clinic navigation"
>
    <div class="brand">
        <span class="brand-mark" aria-hidden="true">H</span>
        <div>
            <h2>HealthSyncAI</h2>
            <p>Clinical Workflow</p>
            <small>Health Information System</small>
        </div>
    </div>

    <div class="nav-title">WORKSPACE</div>

    <nav class="navigation" @click="mobileNavigationOpen = false">
        @if (auth()->user()->isAdmin())
            <a href="{{ route('admin.accounts') }}" @class(['nav-item', 'active' => $active === 'admin'])>
                <span aria-hidden="true">▤</span> User accounts
            </a>
            <a href="{{ route('dashboard') }}" @class(['nav-item', 'active' => $active === 'dashboard'])>
                <span aria-hidden="true">▦</span> Clinic overview
            </a>
        @else
            <a href="{{ route('dashboard') }}" @class(['nav-item', 'active' => $active === 'dashboard'])>
                <span aria-hidden="true">▦</span> Overview
            </a>
            <a href="{{ route('check-ins.index') }}" @class(['nav-item', 'active' => $active === 'check-ins'])>
                <span aria-hidden="true">↪</span> Patient check-in
            </a>
            <a href="{{ route('patients') }}" @class(['nav-item', 'active' => $active === 'patients'])>
                <span aria-hidden="true">♙</span> Patient records
            </a>
            <a href="{{ route('appointments') }}" @class(['nav-item', 'active' => $active === 'appointments'])>
                <span aria-hidden="true">▣</span> Appointments & queue
            </a>
            <a href="{{ route('prescriptions') }}" @class(['nav-item', 'active' => $active === 'prescriptions'])>
                <span aria-hidden="true">Rx</span> Prescription records
            </a>
        @endif
    </nav>

    <div class="sidebar-user">
        <div class="user-icon">♙</div>
        <div>
            <strong>{{ auth()->user()->name }}</strong>
            <small>{{ auth()->user()->roleLabel() }}</small>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" aria-label="Log out">⇥</button>
        </form>
    </div>
</aside>

<livewire:workflow-assistant />
