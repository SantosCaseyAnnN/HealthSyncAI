<x-guest-layout>
    <main class="min-h-screen bg-slate-50">
        <header class="border-b border-slate-200 bg-white">
            <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-4 sm:px-6">
                <a href="{{ route('login') }}" class="font-semibold tracking-tight text-slate-900">HealthSyncAI <span class="ml-2 text-[10px] font-medium uppercase tracking-wide text-slate-500">Clinic portal</span></a>
                <span class="text-[10px] font-semibold uppercase tracking-wide text-slate-500">Public check-in · No account required</span>
            </div>
        </header>

        <div class="mx-auto grid max-w-6xl grid-cols-1 items-start gap-6 px-4 py-8 sm:px-6 lg:grid-cols-[minmax(0,1.35fr)_minmax(280px,.65fr)] lg:py-12">
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-500">Patient check-in</p>
            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">Submit your information</h1>
            <p class="mt-3 text-sm leading-6 text-slate-600">New and returning patients can use this form. Clinic staff will review your details before adding you to the queue. Submitting this form does not register you in the queue.</p>

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
                    <label class="text-sm font-medium text-slate-700">Gender
                        <select name="gender" class="mt-2 block w-full rounded-xl border-slate-300 text-sm">
                            <option value="">Not specified</option>
                            <option value="Female" @selected(old('gender') === 'Female')>Female</option>
                            <option value="Male" @selected(old('gender') === 'Male')>Male</option>
                            <option value="Other" @selected(old('gender') === 'Other')>Other</option>
                        </select>
                        @error('gender') <span class="mt-1 block text-sm text-rose-600">{{ $message }}</span> @enderror
                    </label>
                </div>
                <label class="block text-sm font-medium text-slate-700">Email
                    <input name="email" type="email" value="{{ old('email') }}" maxlength="255" autocomplete="email" class="mt-2 block w-full rounded-xl border-slate-300 text-sm">
                    @error('email') <span class="mt-1 block text-sm text-rose-600">{{ $message }}</span> @enderror
                </label>
                <label class="block text-sm font-medium text-slate-700">Address
                    <input name="address" value="{{ old('address') }}" maxlength="255" autocomplete="street-address" class="mt-2 block w-full rounded-xl border-slate-300 text-sm">
                    @error('address') <span class="mt-1 block text-sm text-rose-600">{{ $message }}</span> @enderror
                </label>
                @unless ($patient)
                    <label class="block text-sm font-medium text-slate-700">Existing patient number <span class="font-normal text-slate-400">Optional for returning patients</span>
                        <input name="existing_patient_number" value="{{ old('existing_patient_number') }}" maxlength="30" autocomplete="off" class="mt-2 block w-full rounded-xl border-slate-300 text-sm">
                        <span class="mt-1 block text-xs font-normal text-slate-500">If you have one, clinic staff can use it to find your existing record.</span>
                        @error('existing_patient_number') <span class="mt-1 block text-sm text-rose-600">{{ $message }}</span> @enderror
                    </label>
                @endunless
                <label class="block text-sm font-medium text-slate-700">Reason for visit <span class="font-normal text-slate-400">Optional</span>
                    <textarea name="reason_for_visit" rows="3" maxlength="2000" class="mt-2 block w-full rounded-xl border-slate-300 text-sm">{{ old('reason_for_visit') }}</textarea>
                    @error('reason_for_visit') <span class="mt-1 block text-sm text-rose-600">{{ $message }}</span> @enderror
                </label>
                <fieldset class="grid grid-cols-1 gap-4 rounded-2xl border border-slate-200 p-4 sm:grid-cols-2 lg:grid-cols-4">
                    <legend class="px-2 text-sm font-semibold text-slate-700">Basic clinic measurements <span class="font-normal text-slate-400">Optional</span></legend>
                    <label class="text-sm font-medium text-slate-700">Height (cm)
                        <input name="height_cm" type="number" step="0.1" min="30" max="250" value="{{ old('height_cm') }}" class="mt-2 block w-full rounded-xl border-slate-300 text-sm">
                        @error('height_cm') <span class="mt-1 block text-sm text-rose-600">{{ $message }}</span> @enderror
                    </label>
                    <label class="text-sm font-medium text-slate-700">Weight (kg)
                        <input name="weight_kg" type="number" step="0.1" min="1" max="500" value="{{ old('weight_kg') }}" class="mt-2 block w-full rounded-xl border-slate-300 text-sm">
                        @error('weight_kg') <span class="mt-1 block text-sm text-rose-600">{{ $message }}</span> @enderror
                    </label>
                    <label class="text-sm font-medium text-slate-700">Blood pressure
                        <input name="blood_pressure" value="{{ old('blood_pressure') }}" maxlength="20" placeholder="120/80" class="mt-2 block w-full rounded-xl border-slate-300 text-sm">
                        @error('blood_pressure') <span class="mt-1 block text-sm text-rose-600">{{ $message }}</span> @enderror
                    </label>
                    <label class="text-sm font-medium text-slate-700">Temperature (C)
                        <input name="temperature_c" type="number" step="0.1" min="25" max="45" value="{{ old('temperature_c') }}" class="mt-2 block w-full rounded-xl border-slate-300 text-sm">
                        @error('temperature_c') <span class="mt-1 block text-sm text-rose-600">{{ $message }}</span> @enderror
                    </label>
                    <label class="text-sm font-medium text-slate-700">Pulse (bpm)
                        <input name="pulse_rate" type="number" min="20" max="300" value="{{ old('pulse_rate') }}" class="mt-2 block w-full rounded-xl border-slate-300 text-sm">
                        @error('pulse_rate') <span class="mt-1 block text-sm text-rose-600">{{ $message }}</span> @enderror
                    </label>
                    <label class="text-sm font-medium text-slate-700">Respiratory rate
                        <input name="respiratory_rate" type="number" min="4" max="80" value="{{ old('respiratory_rate') }}" class="mt-2 block w-full rounded-xl border-slate-300 text-sm">
                        @error('respiratory_rate') <span class="mt-1 block text-sm text-rose-600">{{ $message }}</span> @enderror
                    </label>
                    <label class="text-sm font-medium text-slate-700">Oxygen saturation (%)
                        <input name="oxygen_saturation" type="number" step="0.1" min="50" max="100" value="{{ old('oxygen_saturation') }}" class="mt-2 block w-full rounded-xl border-slate-300 text-sm">
                        @error('oxygen_saturation') <span class="mt-1 block text-sm text-rose-600">{{ $message }}</span> @enderror
                    </label>
                </fieldset>
                <label class="flex items-start gap-3 text-sm leading-5 text-slate-600">
                    <input type="checkbox" name="consent_given" value="1" required @checked(old('consent_given')) class="mt-1 rounded border-slate-300 text-blue-700 focus:ring-blue-600">
                    <span>I consent to the clinic using this information for my visit. Clinic staff will review it before queue registration.
                        @error('consent_given') <span class="block text-rose-600">{{ $message }}</span> @enderror
                    </span>
                </label>
                <button type="submit" class="w-full rounded-lg bg-[#192f52] px-4 py-3 font-semibold text-white hover:bg-[#29456c]">Submit for review <span aria-hidden="true">→</span></button>
            </form>
            <p class="mt-5 text-xs leading-5 text-slate-500">Please do not use this form for urgent medical needs. Contact clinic staff directly for urgent assistance.</p>
        </section>

        <aside class="rounded-2xl bg-[#192f52] p-6 text-white shadow-sm sm:p-7">
            <p class="text-[10px] font-semibold uppercase tracking-wide text-blue-200">A simple start to your visit</p>
            <h2 class="mt-2 text-xl font-semibold">How check-in works</h2>
            <ol class="mt-7 space-y-6">
                <li class="flex gap-4">
                    <span class="text-xs font-semibold text-blue-200">01</span>
                    <span><strong class="block text-sm">Submit your details</strong><small class="mt-1 block leading-5 text-blue-100">Complete this secure check-in form.</small></span>
                </li>
                <li class="flex gap-4">
                    <span class="text-xs font-semibold text-blue-200">02</span>
                    <span><strong class="block text-sm">Staff verification</strong><small class="mt-1 block leading-5 text-blue-100">Clinic staff review your information before registration.</small></span>
                </li>
                <li class="flex gap-4">
                    <span class="text-xs font-semibold text-blue-200">03</span>
                    <span><strong class="block text-sm">Join the queue</strong><small class="mt-1 block leading-5 text-blue-100">You’ll be queued after your details are verified.</small></span>
                </li>
            </ol>
            <div class="mt-7 border-t border-white/15 pt-5 text-xs leading-5 text-blue-100">
                <p>For urgent medical concerns, please speak to clinic staff directly.</p>
                <p class="mt-4 text-blue-200">Your information is visible only to authorized clinic personnel.</p>
            </div>
        </aside>
        </div>
    </main>
</x-guest-layout>
