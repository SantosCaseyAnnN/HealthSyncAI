<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <section class="mb-8 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-semibold text-slate-500">WORKSPACE ACCESS</p>
                <p class="mt-2 text-lg font-semibold text-slate-900">{{ auth()->user()->roleLabel() }}</p>
                <p class="mt-1 text-sm text-slate-600">Account status: {{ auth()->user()->approval_status }}{{ auth()->user()->is_active ? '' : ' · inactive' }}</p>
                @if (auth()->user()->isStudent())
                    <p class="mt-1 text-sm text-slate-600">Quezon City school ID: {{ auth()->user()->school_id }} · {{ auth()->user()->school_id_verified_at ? 'Reviewed' : 'Awaiting administrator review' }}</p>
                    <p class="mt-1 text-xs text-slate-500">Your school ID and role can only be changed by an administrator.</p>
                @elseif (auth()->user()->isClinicStaff())
                    <p class="mt-1 text-xs text-slate-500">Your role can only be changed by an administrator.</p>
                @endif
            </section>

            @if (Laravel\Fortify\Features::canUpdateProfileInformation())
                @livewire('profile.update-profile-information-form')

                <x-section-border />
            @endif

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
                <div class="mt-10 sm:mt-0">
                    @livewire('profile.update-password-form')
                </div>

                <x-section-border />
            @endif

            @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                <div class="mt-10 sm:mt-0">
                    @livewire('profile.two-factor-authentication-form')
                </div>

                <x-section-border />
            @endif

            <div class="mt-10 sm:mt-0">
                @livewire('profile.logout-other-browser-sessions-form')
            </div>

            @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
                <x-section-border />

                <div class="mt-10 sm:mt-0">
                    @livewire('profile.delete-user-form')
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
