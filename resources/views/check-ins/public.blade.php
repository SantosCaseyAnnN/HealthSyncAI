<x-guest-layout>
    <main class="min-h-screen bg-slate-50 px-4 py-10 sm:px-6">
        <div class="mx-auto max-w-2xl rounded-3xl border border-slate-200 bg-white p-6 shadow-xl shadow-slate-200/60 sm:p-10">
            <a href="{{ route('login') }}" class="text-sm font-semibold text-blue-700">HealthSyncAI</a>
            <p class="mt-8 text-sm font-semibold text-blue-700">CLINIC CHECK-IN</p>
            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">Share your check-in details</h1>
            <p class="mt-3 text-sm leading-6 text-slate-600">This QR code is linked to a clinic record. Submit your basic details for staff review. Submitting this form does not place you in the queue; clinic staff will verify it first.</p>

            @if (session('status'))
                <p class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800" role="status">{{ session('status') }}</p>
            @endif

            <form method="POST" action="{{ request()->fullUrl() }}" class="mt-7 space-y-5">
                @csrf
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <label class="text-sm font-medium text-slate-700">First name
                        <input name="first_name" value="{{ old('first_name') }}" required maxlength="100" autocomplete="given-name" class="mt-2 block w-full rounded-xl border-slate-300 text-sm">
                        @error('first_name') <span class="mt-1 block text-sm text-rose-600">{{ $message }}</span> @enderror
                    </label>
                    <label class="text-sm font-medium text-slate-700">Last name
                        <input name="last_name" value="{{ old('last_name') }}" required maxlength="100" autocomplete="family-name" class="mt-2 block w-full rounded-xl border-slate-300 text-sm">
                        @error('last_name') <span class="mt-1 block text-sm text-rose-600">{{ $message }}</span> @enderror
                    </label>
                    <label class="text-sm font-medium text-slate-700">Date of birth
                        <input name="date_of_birth" type="date" value="{{ old('date_of_birth') }}" class="mt-2 block w-full rounded-xl border-slate-300 text-sm">
                        @error('date_of_birth') <span class="mt-1 block text-sm text-rose-600">{{ $message }}</span> @enderror
                    </label>
                    <label class="text-sm font-medium text-slate-700">Phone
                        <input name="phone" type="tel" value="{{ old('phone') }}" maxlength="30" autocomplete="tel" class="mt-2 block w-full rounded-xl border-slate-300 text-sm">
                        @error('phone') <span class="mt-1 block text-sm text-rose-600">{{ $message }}</span> @enderror
                    </label>
                </div>
                <label class="block text-sm font-medium text-slate-700">Email
                    <input name="email" type="email" value="{{ old('email') }}" maxlength="255" autocomplete="email" class="mt-2 block w-full rounded-xl border-slate-300 text-sm">
                    @error('email') <span class="mt-1 block text-sm text-rose-600">{{ $message }}</span> @enderror
                </label>
                <label class="block text-sm font-medium text-slate-700">Reason for visit <span class="font-normal text-slate-400">Optional</span>
                    <textarea name="reason_for_visit" rows="3" maxlength="2000" class="mt-2 block w-full rounded-xl border-slate-300 text-sm">{{ old('reason_for_visit') }}</textarea>
                    @error('reason_for_visit') <span class="mt-1 block text-sm text-rose-600">{{ $message }}</span> @enderror
                </label>
                <button type="submit" class="w-full rounded-xl bg-blue-700 px-4 py-3 font-semibold text-white hover:bg-blue-800">Send for clinic review</button>
            </form>
            <p class="mt-5 text-xs leading-5 text-slate-500">Please do not use this form for urgent medical needs. Contact clinic staff directly for urgent assistance.</p>
        </div>
    </main>
</x-guest-layout>
