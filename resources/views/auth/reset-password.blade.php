<x-guest-layout>
    <main class="min-h-screen bg-white lg:grid lg:grid-cols-[.8fr_1.2fr]">
        <section class="hidden min-h-screen flex-col justify-between bg-[#192f52] p-12 text-white lg:flex">
            <a href="{{ route('login') }}" class="text-xl font-bold tracking-tight text-white">HealthSyncAI</a>
            <div class="max-w-md">
                <h1 class="text-4xl font-semibold leading-tight tracking-tight">Better care.<br>A clearer workflow.</h1>
                <p class="mt-5 text-sm leading-6 text-blue-100">One secure place for clinic teams to manage care and coordinate patient visits.</p>
            </div>
            <p class="text-xs text-blue-200">Role-based access. Patient-first privacy.</p>
        </section>

        <section class="flex min-h-screen items-center justify-center px-5 py-12 sm:px-8">
            <div class="w-full max-w-md">
                <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-500 lg:text-center">Quezon City / Health &amp; Learning</p>
                <div class="mt-12">
                    <h2 class="text-3xl font-semibold tracking-tight text-slate-900">Choose a new password</h2>
                    <p class="mt-3 text-sm leading-6 text-slate-500">Create a new password for your HealthSyncAI account.</p>
                </div>

                <x-validation-errors class="mt-6 rounded-lg border border-rose-200 bg-rose-50 p-3 text-sm text-rose-700" />

                <form method="POST" action="{{ route('password.update') }}" class="mt-7 space-y-5">
                    @csrf
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">
                    <div>
                        <label for="email" class="mb-2 block text-sm font-medium text-slate-700">Email address</label>
                        <input id="email" name="email" type="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username" class="block w-full rounded-lg border-slate-300 px-4 py-3 text-sm focus:border-blue-700 focus:ring-blue-700">
                    </div>
                    <div>
                        <label for="password" class="mb-2 block text-sm font-medium text-slate-700">New password</label>
                        <input id="password" name="password" type="password" required autocomplete="new-password" class="block w-full rounded-lg border-slate-300 px-4 py-3 text-sm focus:border-blue-700 focus:ring-blue-700">
                    </div>
                    <div>
                        <label for="password_confirmation" class="mb-2 block text-sm font-medium text-slate-700">Confirm password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="block w-full rounded-lg border-slate-300 px-4 py-3 text-sm focus:border-blue-700 focus:ring-blue-700">
                    </div>
                    <button type="submit" class="w-full rounded-lg bg-[#192f52] px-4 py-3 text-sm font-semibold text-white hover:bg-[#29456c]">Reset password</button>
                </form>
            </div>
        </section>
    </main>
</x-guest-layout>
