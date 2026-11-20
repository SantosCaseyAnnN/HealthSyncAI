<x-guest-layout>
    <main class="min-h-screen bg-white lg:grid lg:grid-cols-[.8fr_1.2fr]">
        <section class="hidden min-h-screen flex-col justify-between bg-[#192f52] p-12 text-white lg:flex">
            <a href="{{ route('login') }}" class="text-xl font-bold tracking-tight text-white">HealthSyncAI</a>
            <div class="max-w-md">
                <h1 class="text-4xl font-semibold leading-tight tracking-tight">Better care.<br>A clearer workflow.</h1>
                <p class="mt-5 text-sm leading-6 text-blue-100">One secure place for clinic teams to manage care and coordinate patient visits.</p>
                <div class="mt-10 rounded-xl bg-white/10 p-5">
                    <p class="text-[10px] font-semibold uppercase tracking-wide text-blue-200">Connected care, from the start</p>
                    <ol class="mt-4 space-y-3 text-sm">
                        <li>01&nbsp;&nbsp; Patient check-in</li>
                        <li>02&nbsp;&nbsp; Staff verification</li>
                        <li>03&nbsp;&nbsp; Queue &amp; care</li>
                    </ol>
                </div>
            </div>
            <p class="text-xs text-blue-200">Role-based access. Patient-first privacy.</p>
        </section>

        <section class="flex min-h-screen items-center justify-center px-5 py-12 sm:px-8">
            <div class="w-full max-w-md">
                <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-500 lg:text-center">Quezon City / Health &amp; Learning</p>
                <div class="mt-12">
                    <h2 class="text-3xl font-semibold tracking-tight text-slate-900">Forgot your password?</h2>
                    <p class="mt-3 text-sm leading-6 text-slate-500">Enter your registered email address. We’ll send instructions to reset your password.</p>
                </div>

                @session('status')
                    <p class="mt-6 rounded-lg border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-800" role="status">{{ $value }}</p>
                @endsession

                <x-validation-errors class="mt-6 rounded-lg border border-rose-200 bg-rose-50 p-3 text-sm text-rose-700" />

                <form method="POST" action="{{ route('password.email') }}" class="mt-7 space-y-5">
                    @csrf
                    <div>
                        <label for="email" class="mb-2 block text-sm font-medium text-slate-700">Email address</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="block w-full rounded-lg border-slate-300 px-4 py-3 text-sm focus:border-blue-700 focus:ring-blue-700" placeholder="you@example.com">
                    </div>
                    <button type="submit" class="w-full rounded-lg bg-[#192f52] px-4 py-3 text-sm font-semibold text-white hover:bg-[#29456c]">Send reset instructions</button>
                </form>

                <a href="{{ route('login') }}" class="mt-6 inline-block text-sm font-semibold text-blue-800 hover:text-blue-950">← Back to sign in</a>
                <p class="mt-10 text-xs text-slate-500">For this prototype, no email will be sent.</p>
            </div>
        </section>
    </main>
</x-guest-layout>
