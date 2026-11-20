<x-guest-layout>
    <main class="min-h-screen bg-slate-50 lg:grid lg:grid-cols-[1.05fr_.95fr]">
        <section class="relative hidden min-h-screen overflow-hidden bg-[#192f52] p-12 text-white lg:flex lg:flex-col lg:justify-between">
            <div class="absolute -right-28 -top-20 size-96 rounded-full bg-cyan-300/20 blur-3xl"></div>
            <div class="relative flex items-center gap-3">
                <span class="grid size-12 place-items-center rounded-2xl bg-white/15 text-2xl font-bold">H</span>
                <div>
                    <p class="text-xl font-bold tracking-tight">HealthSyncAI</p>
                    <p class="text-sm text-blue-100">Clinic workflow, in sync</p>
                </div>
            </div>
            <div class="relative max-w-xl">
                <p class="mb-4 text-sm font-semibold uppercase tracking-[.22em] text-cyan-200">A calmer way to care</p>
                <h1 class="text-5xl font-semibold leading-tight tracking-tight">Your care team, connected.</h1>
                <p class="mt-5 max-w-lg text-lg leading-8 text-blue-100">Secure tools for clinic staff and administrators.</p>
            </div>
            <p class="relative text-sm text-blue-200">Secure access for approved HealthSyncAI accounts</p>
        </section>

        <section class="flex min-h-screen items-center justify-center px-5 py-12 sm:px-8">
            <div class="w-full max-w-md">
                <div class="mb-8 lg:hidden">
                    <p class="text-xl font-bold text-slate-900">HealthSyncAI</p>
                    <p class="mt-1 text-sm text-slate-500">Clinic workflow, in sync</p>
                </div>
                <div class="rounded-3xl border border-slate-200 bg-white p-7 shadow-xl shadow-slate-200/60 sm:p-9">
                    <div class="mb-8">
                        <p class="text-sm font-semibold text-blue-700">WELCOME BACK</p>
                        <h2 class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">Sign in</h2>
                        <p class="mt-2 text-sm text-slate-500">Use your registered email and password.</p>
                    </div>

                    @if (session('status'))
                        <p class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-800" role="status">{{ session('status') }}</p>
                    @endif
                    <x-validation-errors class="mb-5 rounded-xl border border-rose-200 bg-rose-50 p-3 text-sm text-rose-700" />

                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf
                        <div>
                            <label for="email" class="mb-2 block text-sm font-medium text-slate-700">Email address</label>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-blue-600 focus:ring-blue-600" placeholder="you@example.com">
                        </div>
                        <div x-data="{ visible: false }">
                            <div class="mb-2 flex items-center justify-between gap-3">
                                <label for="password" class="text-sm font-medium text-slate-700">Password</label>
                                <a href="{{ route('password.request') }}" class="text-sm font-medium text-blue-700 hover:text-blue-900">Forgot password?</a>
                            </div>
                            <div class="relative">
                                <input id="password" name="password" x-bind:type="visible ? 'text' : 'password'" required autocomplete="current-password" class="block w-full rounded-xl border-slate-300 px-4 py-3 pr-16 text-sm shadow-sm focus:border-blue-600 focus:ring-blue-600" placeholder="Enter your password">
                                <button type="button" class="absolute inset-y-0 right-3 text-sm font-medium text-slate-500 hover:text-slate-800" x-on:click="visible = !visible" x-text="visible ? 'Hide' : 'Show'" aria-label="Show or hide password"></button>
                            </div>
                        </div>
                        <label class="flex items-center gap-2 text-sm text-slate-600">
                            <input name="remember" type="checkbox" class="rounded border-slate-300 text-blue-700 focus:ring-blue-600">
                            Remember me
                        </label>
                        <button type="submit" class="w-full rounded-xl bg-[#192f52] px-4 py-3 font-semibold text-white transition hover:bg-[#29456c]">Sign in</button>
                    </form>

                    <p class="mt-7 text-center text-sm text-slate-600">New to HealthSyncAI?
                        <a href="{{ route('register') }}" class="font-semibold text-blue-700 hover:text-blue-900">Create an account</a>
                    </p>
                </div>
                <p class="mt-6 text-center text-xs leading-5 text-slate-500">Only approved and active accounts can access their role workspace.</p>
            </div>
        </section>
    </main>
</x-guest-layout>
