<x-guest-layout>
    <div class="min-h-screen bg-[#1d1d20] p-5 md:p-8">
        <div class="mx-auto max-w-[1200px] overflow-hidden rounded-[18px] border border-[#2a2a2d] bg-white shadow-2xl shadow-black/20">
            <div class="grid min-h-[780px] grid-cols-1 lg:grid-cols-2">
                <div class="relative overflow-hidden bg-gradient-to-r from-[#0ea5d6] via-[#32c4d7] to-[#dff7fb]">
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(255,255,255,0.28),transparent_38%)]"></div>
                    <div class="absolute inset-0 bg-gradient-to-r from-[#0fa4c9] via-[#43d1d8] to-[#eafdfd] opacity-90"></div>
                </div>

                <div class="flex items-center justify-center bg-[#f5f5f5] p-6 sm:p-10">
                    <div class="w-full max-w-[360px] rounded-[20px] bg-[#e8e5f5] px-6 py-8 shadow-[0_12px_30px_rgba(15,23,42,0.08)]">
                        <x-validation-errors class="mb-4 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-700" />

                        <form method="POST" action="{{ route('register') }}" class="space-y-4">
                            @csrf

                            <div>
                                <label for="name" class="mb-1 block text-sm font-medium text-slate-700">Full Name</label>
                                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" class="block w-full rounded-xl border border-[#dfe7ff] bg-[#dfe7ff]/80 px-3 py-2.5 text-base text-slate-700 shadow-inner shadow-slate-200/50 outline-none transition placeholder:text-slate-400 focus:border-[#c5d0ff] focus:bg-white" placeholder="Value" />
                            </div>

                            <div>
                                <label for="user_id" class="mb-1 block text-sm font-medium text-slate-700">User ID</label>
                                <input id="user_id" type="text" name="user_id" value="{{ old('user_id') }}" required class="block w-full rounded-xl border border-[#dfe7ff] bg-[#dfe7ff]/80 px-3 py-2.5 text-base text-slate-700 shadow-inner shadow-slate-200/50 outline-none transition placeholder:text-slate-400 focus:border-[#c5d0ff] focus:bg-white" placeholder="Value" />
                            </div>

                            <div>
                                <label for="email" class="mb-1 block text-sm font-medium text-slate-700">Email</label>
                                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" class="block w-full rounded-xl border border-[#dfe7ff] bg-[#dfe7ff]/80 px-3 py-2.5 text-base text-slate-700 shadow-inner shadow-slate-200/50 outline-none transition placeholder:text-slate-400 focus:border-[#c5d0ff] focus:bg-white" placeholder="Value" />
                            </div>

                            <div>
                                <label for="password" class="mb-1 block text-sm font-medium text-slate-700">Password</label>
                                <input id="password" type="password" name="password" required autocomplete="new-password" class="block w-full rounded-xl border border-[#dfe7ff] bg-[#dfe7ff]/80 px-3 py-2.5 text-base text-slate-700 shadow-inner shadow-slate-200/50 outline-none transition placeholder:text-slate-400 focus:border-[#c5d0ff] focus:bg-white" placeholder="Value" />
                            </div>

                            <div>
                                <label for="password_confirmation" class="mb-1 block text-sm font-medium text-slate-700">Confirm Password</label>
                                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="block w-full rounded-xl border border-[#dfe7ff] bg-[#dfe7ff]/80 px-3 py-2.5 text-base text-slate-700 shadow-inner shadow-slate-200/50 outline-none transition placeholder:text-slate-400 focus:border-[#c5d0ff] focus:bg-white" placeholder="Value" />
                            </div>

                            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                                <div class="pt-1">
                                    <label class="flex items-center gap-2 text-sm text-slate-700">
                                        <input type="checkbox" name="terms" required class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-500" />
                                        <span>
                                            I agree to the <a href="{{ route('terms.show') }}" class="font-medium underline underline-offset-2">Terms of Service</a>
                                            and <a href="{{ route('policy.show') }}" class="font-medium underline underline-offset-2">Privacy Policy</a>
                                        </span>
                                    </label>
                                </div>
                            @endif

                            <button type="submit" class="mt-2 w-full rounded-xl bg-[#2b2b2d] px-4 py-3 text-base font-semibold text-white shadow-lg shadow-slate-400/20 transition hover:bg-[#1d1d20]">
                                Sign In
                            </button>

                            <div class="pt-1 text-center text-sm text-slate-700">
                                Already have an account? <a href="{{ route('login') }}" class="font-medium text-slate-900 underline underline-offset-2">Log In Here</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
