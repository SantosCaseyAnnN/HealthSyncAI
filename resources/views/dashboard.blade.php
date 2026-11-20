<x-app-layout>
<div @class(['healthsync-dashboard', 'no-sidebar' => ! auth()->user()->hasWorkspaceAccess()])>
    @if (auth()->user()->hasWorkspaceAccess())
        @include('livewire.partials.clinical-navigation', ['active' => 'dashboard'])
    @endif

    <div @class([
        'main-content' => auth()->user()->hasWorkspaceAccess(),
        'pending-content' => ! auth()->user()->hasWorkspaceAccess(),
    ])>
        @if (auth()->user()->hasWorkspaceAccess())
            @include('livewire.partials.workspace-topbar', ['section' => auth()->user()->isAdmin() ? 'Administration / Clinic overview' : 'Clinic / Overview'])
        @else
            <header class="pending-header">
                <a href="{{ route('login') }}" class="pending-brand"><span class="brand-mark" aria-hidden="true">H</span> HealthSyncAI</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="small-outline-button">Sign out</button>
                </form>
            </header>
        @endif

        <div class="mx-auto max-w-7xl space-y-6">
            @if (session('status'))
                <p class="rounded-2xl border border-blue-200 bg-blue-50 px-5 py-4 text-sm text-blue-900" role="status">{{ session('status') }}</p>
            @endif

            @if (! auth()->user()->hasWorkspaceAccess())
                <section class="rounded-3xl border border-amber-200 bg-amber-50 p-6 sm:p-8">
                    <p class="text-sm font-semibold uppercase tracking-wide text-amber-800">Workspace access is {{ auth()->user()->approval_status }}</p>
                    <h2 class="mt-2 text-2xl font-semibold text-slate-900">
                        @if (auth()->user()->approval_status === \App\Models\User::APPROVAL_PENDING)
                            Your application is awaiting review
                        @elseif (auth()->user()->approval_status === \App\Models\User::APPROVAL_REJECTED)
                            Your application was not accepted
                        @elseif (! auth()->user()->is_active)
                            Your account is inactive
                        @else
                            Your application is awaiting review
                        @endif
                    </h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-700">You can sign in to check your application status, but workspace data remains unavailable until an administrator approves and activates your account.</p>
                </section>
            @elseif (auth()->user()->isClinicStaff())
                <section class="rounded-3xl border border-blue-100 bg-white p-6 shadow-sm sm:p-8">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold text-blue-700">CLINIC WORKFLOW</p>
                            <h2 class="mt-2 text-2xl font-semibold text-slate-900">Good morning, {{ auth()->user()->first_name ?: auth()->user()->name }}</h2>
                            <p class="mt-2 text-sm text-slate-500">Patient and care-team tools for authorized clinic staff.</p>
                        </div>
                        <a href="{{ route('patients') }}" class="rounded-lg bg-[#192f52] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#29456c]">Open patient directory</a>
                    </div>
                    <div class="mt-7 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                        @foreach ($stats as $label => $value)
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                                <p class="text-sm text-slate-500">{{ $label }}</p>
                                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $value }}</p>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-6 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
                        <a href="{{ route('check-ins.index') }}" class="rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700 hover:border-blue-300 hover:bg-blue-50">Review patient check-ins</a>
                        <a href="{{ route('appointments') }}" class="rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700 hover:border-blue-300 hover:bg-blue-50">Appointments and queue</a>
                        <a href="{{ route('medical-records') }}" class="rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700 hover:border-blue-300 hover:bg-blue-50">Medical records and labs</a>
                        <a href="{{ route('laboratory') }}" class="rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700 hover:border-blue-300 hover:bg-blue-50">Laboratory orders</a>
                        <a href="{{ route('prescriptions') }}" class="rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700 hover:border-blue-300 hover:bg-blue-50">Prescription records</a>
                    </div>
                </section>
                <section class="grid grid-cols-1 gap-6 lg:grid-cols-[1.4fr_.8fr]">
                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <h2 class="text-lg font-semibold text-slate-900">Patient check-in review</h2>
                                <p class="mt-1 text-sm text-slate-500">Verify patient details before adding check-ins to the queue.</p>
                            </div>
                            <a href="{{ route('check-ins.index') }}" class="text-sm font-semibold text-[#315d96]">View all</a>
                        </div>
                        <div class="mt-4 divide-y divide-slate-100">
                            @forelse ($pendingCheckIns as $checkIn)
                                <a wire:key="dashboard-check-in-{{ $checkIn->id }}" href="{{ route('check-ins.index') }}" class="flex items-center justify-between gap-4 py-3">
                                    <span>
                                        <span class="block text-sm font-semibold text-slate-900">{{ $checkIn->first_name }} {{ $checkIn->last_name }}</span>
                                        <span class="block text-xs text-slate-500">{{ $checkIn->created_at->format('h:i A') }} · QR check-in</span>
                                    </span>
                                    <span class="text-sm font-semibold text-[#315d96]">Review →</span>
                                </a>
                            @empty
                                <p class="py-5 text-sm text-slate-500">No check-ins are awaiting review.</p>
                            @endforelse
                        </div>
                    </div>
                    <aside class="rounded-2xl bg-[#192f52] p-6 text-white">
                        <p class="text-xs font-semibold uppercase tracking-wide text-blue-200">Workflow estimate</p>
                        <h2 class="mt-2 text-xl font-semibold">Estimated patient wait</h2>
                        <p class="mt-4 text-4xl font-semibold">{{ $estimatedQueueWait }} <span class="text-base font-medium text-slate-300">min</span></p>
                        <p class="mt-4 border-t border-white/15 pt-4 text-xs leading-5 text-slate-300">A simple queue-position estimate for scheduling support. It is not an AI model or medical judgment.</p>
                    </aside>
                </section>
                <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-900">Live queue</h2>
                            <p class="mt-1 text-sm text-slate-500">Queue monitoring for today.</p>
                        </div>
                        <a href="{{ route('check-ins.index', ['statusFilter' => 'queue']) }}" class="text-sm font-semibold text-blue-700">Manage queue →</a>
                    </div>
                    <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-5">
                        @forelse ($todayQueue as $queueEntry)
                            <article wire:key="dashboard-queue-{{ $queueEntry->id }}" class="rounded-2xl bg-slate-50 p-4">
                                <p class="text-xs font-semibold text-blue-700">Q-{{ str_pad((string) $queueEntry->queue_number, 3, '0', STR_PAD_LEFT) }}</p>
                                <p class="mt-2 text-sm font-semibold text-slate-900">{{ $queueEntry->patient->first_name }} {{ $queueEntry->patient->last_name }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ str($queueEntry->status)->replace('_', ' ')->title() }}</p>
                            </article>
                        @empty
                            <p class="py-4 text-sm text-slate-500">No patients are currently in the queue.</p>
                        @endforelse
                    </div>
                </section>
            @elseif (auth()->user()->isAdmin())
                <section class="rounded-3xl border border-blue-100 bg-white p-6 shadow-sm sm:p-8">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold text-blue-700">ADMINISTRATION</p>
                            <h2 class="mt-2 text-2xl font-semibold text-slate-900">Clinic overview</h2>
                            <p class="mt-2 text-sm text-slate-500">Review applications and maintain clinic staff and administrator accounts.</p>
                        </div>
                        <a href="{{ route('admin.accounts') }}" class="rounded-lg bg-[#192f52] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#29456c]">Manage accounts</a>
                    </div>
                    <p class="mt-6 text-sm text-slate-600">This overview shows aggregate clinic activity only. Patient records and queue details remain restricted to authorized clinic staff.</p>
                    <div class="mt-7 grid grid-cols-1 gap-4 sm:grid-cols-3">
                        @foreach ($stats as $label => $value)
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                                <p class="text-sm text-slate-500">{{ $label }}</p>
                                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $value }}</p>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            @if ($notifications->isNotEmpty())
                <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-slate-900">Account messages</h2>
                    <div class="mt-4 space-y-3">
                        @foreach ($notifications as $notification)
                            <article wire:key="notification-{{ $notification->id }}" class="rounded-xl bg-slate-50 p-4">
                                <p class="text-sm font-semibold text-slate-900">Application {{ $notification->data['decision'] ?? 'update' }}</p>
                                <p class="mt-1 text-sm leading-6 text-slate-600">{{ $notification->data['message'] ?? '' }}</p>
                                <p class="mt-2 text-xs text-slate-400">{{ $notification->created_at->diffForHumans() }}</p>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </div>
</div>
</x-app-layout>
