<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-sm font-semibold text-blue-700">{{ auth()->user()->roleLabel() }} workspace</p>
                <h1 class="mt-1 text-2xl font-semibold tracking-tight text-slate-900">Hello, {{ auth()->user()->first_name ?: auth()->user()->name }}</h1>
            </div>
            @if (auth()->user()->hasWorkspaceAccess())
                <span class="rounded-full bg-emerald-50 px-3 py-1.5 text-sm font-medium text-emerald-700">Account active</span>
            @endif
        </div>
    </x-slot>

    <div class="min-h-[calc(100vh-5rem)] bg-slate-50 px-4 py-8 sm:px-6 lg:px-8">
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
                            <h2 class="mt-2 text-2xl font-semibold text-slate-900">Today at your clinic</h2>
                            <p class="mt-2 text-sm text-slate-500">Patient and care-team tools for authorized clinic staff.</p>
                        </div>
                        <a href="{{ route('patients') }}" class="rounded-xl bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-800">Open patient directory</a>
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
            @elseif (auth()->user()->isAdmin())
                <section class="rounded-3xl border border-blue-100 bg-white p-6 shadow-sm sm:p-8">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold text-blue-700">ADMINISTRATION</p>
                            <h2 class="mt-2 text-2xl font-semibold text-slate-900">Manage workspace access</h2>
                            <p class="mt-2 text-sm text-slate-500">Review applications and maintain clinic staff and administrator accounts.</p>
                        </div>
                        <a href="{{ route('admin.accounts') }}" class="rounded-xl bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-800">Manage accounts</a>
                    </div>
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
</x-app-layout>
