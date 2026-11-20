<div class="workspace-topbar">
    <button
        type="button"
        class="mobile-nav-toggle"
        aria-label="Toggle navigation"
        aria-controls="clinical-navigation"
        :aria-expanded="mobileNavigationOpen ? 'true' : 'false'"
        @click="mobileNavigationOpen = ! mobileNavigationOpen"
    >
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>
    <span>{{ $section }}</span>
    <time datetime="{{ now()->toDateString() }}">{{ now()->format('l, d F Y') }}</time>
</div>
