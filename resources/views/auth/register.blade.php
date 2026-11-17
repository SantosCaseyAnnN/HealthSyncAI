<x-guest-layout>
    <main class="min-h-screen bg-slate-50 px-4 py-10 sm:px-6">
        <div class="mx-auto grid min-h-[calc(100vh-5rem)] max-w-6xl overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl shadow-slate-200/70 lg:grid-cols-[.8fr_1.2fr]">
            <section class="relative hidden flex-col justify-between overflow-hidden bg-gradient-to-br from-sky-700 via-blue-700 to-indigo-800 p-10 text-white lg:flex">
                <div class="absolute -bottom-28 -left-24 size-96 rounded-full bg-cyan-300/20 blur-3xl"></div>
                <div class="relative">
                    <span class="grid size-12 place-items-center rounded-2xl bg-white/15 text-2xl font-bold">H</span>
                    <p class="mt-4 text-xl font-bold">HealthSyncAI</p>
                </div>
                <div class="relative">
                    <p class="text-sm font-semibold uppercase tracking-[.22em] text-cyan-200">Join the care team</p>
                    <h1 class="mt-4 text-4xl font-semibold leading-tight tracking-tight">One workspace for better care coordination.</h1>
                    <p class="mt-5 leading-7 text-blue-100">Choose your role to apply for the right HealthSyncAI workspace.</p>
                </div>
                <p class="relative text-sm text-blue-200">Every new account is reviewed by an administrator.</p>
            </section>

            <section class="flex items-center justify-center px-5 py-8 sm:px-10 lg:px-14">
                <div class="w-full max-w-xl" x-data="{ role: '{{ old('role', 'clinic_staff') }}' }">
                    <div class="mb-7">
                        <p class="text-sm font-semibold text-blue-700">ACCOUNT APPLICATION</p>
                        <h2 class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">Create your account</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-500">Clinic staff and student accounts need administrator approval before workspace access.</p>
                    </div>
                    <x-validation-errors class="mb-5 rounded-xl border border-rose-200 bg-rose-50 p-3 text-sm text-rose-700" />

                    <form method="POST" action="{{ route('register') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label for="role" class="mb-2 block text-sm font-medium text-slate-700">I am registering as</label>
                            <select id="role" name="role" x-model="role" required class="block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-blue-600 focus:ring-blue-600">
                                <option value="clinic_staff">Clinic Staff</option>
                                <option value="student">Student</option>
                                <option value="admin">Admin</option>
                            </select>
                            @error('role') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                            <div>
                                <label for="first_name" class="mb-2 block text-sm font-medium text-slate-700">First name</label>
                                <input id="first_name" name="first_name" value="{{ old('first_name') }}" required autocomplete="given-name" class="block w-full rounded-xl border-slate-300 px-3 py-3 text-sm shadow-sm focus:border-blue-600 focus:ring-blue-600">
                                @error('first_name') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="middle_name" class="mb-2 block text-sm font-medium text-slate-700">Middle name <span class="font-normal text-slate-400">Optional</span></label>
                                <input id="middle_name" name="middle_name" value="{{ old('middle_name') }}" autocomplete="additional-name" class="block w-full rounded-xl border-slate-300 px-3 py-3 text-sm shadow-sm focus:border-blue-600 focus:ring-blue-600">
                                @error('middle_name') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="last_name" class="mb-2 block text-sm font-medium text-slate-700">Last name</label>
                                <input id="last_name" name="last_name" value="{{ old('last_name') }}" required autocomplete="family-name" class="block w-full rounded-xl border-slate-300 px-3 py-3 text-sm shadow-sm focus:border-blue-600 focus:ring-blue-600">
                                @error('last_name') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label for="email" class="mb-2 block text-sm font-medium text-slate-700">
                                <span x-show="role === 'clinic_staff'">Clinic email</span>
                                <span x-show="role === 'student'">Account email</span>
                                <span x-show="role === 'admin'">Admin email</span>
                            </label>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email" class="block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-blue-600 focus:ring-blue-600" placeholder="you@example.com">
                            @error('email') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div x-cloak x-show="role === 'student'" class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label for="school_id" class="mb-2 block text-sm font-medium text-slate-700">Quezon City school ID</label>
                                <input id="school_id" name="school_id" value="{{ old('school_id') }}" x-bind:required="role === 'student'" x-bind:disabled="role !== 'student'" class="block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-blue-600 focus:ring-blue-600" placeholder="School ID number">
                                @error('school_id') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="year_level" class="mb-2 block text-sm font-medium text-slate-700">Current year level</label>
                                <select id="year_level" name="year_level" x-bind:required="role === 'student'" x-bind:disabled="role !== 'student'" class="block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-blue-600 focus:ring-blue-600">
                                    <option value="">Select year level</option>
                                    @foreach (['First', 'Second', 'Third', 'Fourth', 'Fifth', 'Higher', 'Graduate/Postgrad'] as $yearLevel)
                                        <option value="{{ $yearLevel }}" @selected(old('year_level') === $yearLevel)>{{ $yearLevel }}</option>
                                    @endforeach
                                </select>
                                @error('year_level') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label for="password" class="mb-2 block text-sm font-medium text-slate-700">Password</label>
                                <input id="password" name="password" type="password" required autocomplete="new-password" class="block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-blue-600 focus:ring-blue-600">
                                @error('password') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="password_confirmation" class="mb-2 block text-sm font-medium text-slate-700">Confirm password</label>
                                <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-blue-600 focus:ring-blue-600">
                            </div>
                        </div>

                        @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                            <label class="flex items-start gap-2 text-sm leading-5 text-slate-600">
                                <input type="checkbox" name="terms" required class="mt-1 rounded border-slate-300 text-blue-700 focus:ring-blue-600">
                                <span>I agree to the <a href="{{ route('terms.show') }}" class="font-medium text-blue-700 underline">Terms of Service</a> and <a href="{{ route('policy.show') }}" class="font-medium text-blue-700 underline">Privacy Policy</a>.</span>
                            </label>
                        @endif

                        <div class="rounded-xl border border-blue-100 bg-blue-50 px-4 py-3 text-sm leading-5 text-blue-900">
                            Applications are reviewed by an administrator. Access is enabled only after approval. Student accounts only receive the simulation workspace.
                        </div>
                        <button type="submit" class="w-full rounded-xl bg-blue-700 px-4 py-3 font-semibold text-white shadow-lg shadow-blue-700/20 transition hover:bg-blue-800">Submit application</button>
                    </form>

                    <p class="mt-6 text-center text-sm text-slate-600">Already have an account?
                        <a href="{{ route('login') }}" class="font-semibold text-blue-700 hover:text-blue-900">Sign in</a>
                    </p>
                </div>
            </section>
        </div>
    </main>
</x-guest-layout>
