<aside class="sidebar">
    <div class="brand">
        <h2>HealthSyncAI</h2>
        <p>Clinical Workflow Health</p>
        <small>Information System</small>
    </div>

    <div class="nav-title">NAVIGATION</div>

    <nav class="navigation">
        <a href="{{ route('dashboard') }}" class="nav-item">
            <span>▦</span> Dashboard
        </a>
        <a href="{{ route('patients') }}" @class(['nav-item', 'active' => $active === 'patients'])>
            <span>♙</span> Patients
        </a>
        <a href="{{ route('appointments') }}" @class(['nav-item', 'active' => $active === 'appointments'])>
            <span>▣</span> Appointments
        </a>
        <a href="{{ route('medical-records') }}" @class(['nav-item', 'active' => $active === 'medical-records'])>
            <span>▤</span> Medical Records
        </a>
        <a href="{{ route('laboratory') }}" @class(['nav-item', 'active' => $active === 'laboratory'])>
            <span>♧</span> Laboratory
        </a>
    </nav>

    <div class="sidebar-user">
        <div class="user-icon">♙</div>
        <strong>{{ auth()->user()->name }}</strong>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" aria-label="Log out">⇥</button>
        </form>
    </div>
</aside>
