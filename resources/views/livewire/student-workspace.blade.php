<div class="min-h-screen bg-slate-50 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl space-y-6">
        <header class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <a href="{{ route('dashboard') }}" class="text-sm font-medium text-blue-700 hover:text-blue-900">← Dashboard</a>
                <p class="mt-5 text-sm font-semibold text-blue-700">ALLIED-HEALTH SIMULATION</p>
                <h1 class="mt-1 text-3xl font-semibold tracking-tight text-slate-900">Practice workspace</h1>
                <p class="mt-2 text-sm text-slate-500">Fictional practice cases only. This workspace has no connection to clinic records.</p>
            </div>
            <span class="rounded-full border border-amber-200 bg-amber-50 px-3 py-1.5 text-xs font-semibold uppercase tracking-wide text-amber-800">Simulation only</span>
        </header>

        @if (session()->has('status'))
            <p class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">{{ session('status') }}</p>
        @endif

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-[.8fr_1.2fr]">
            <section class="space-y-3">
                <h2 class="px-1 text-sm font-semibold uppercase tracking-wide text-slate-500">Fictional cases</h2>
                @foreach ($cases as $case)
                    <button type="button" wire:key="practice-case-{{ $case['id'] }}" wire:click="$set('selectedCase', '{{ $case['id'] }}')" @class([
                        'w-full rounded-2xl border p-5 text-left transition',
                        'border-blue-300 bg-blue-50 shadow-sm' => $selectedCase === $case['id'],
                        'border-slate-200 bg-white hover:border-blue-200' => $selectedCase !== $case['id'],
                    ])>
                        <span class="text-xs font-semibold uppercase tracking-wide text-blue-700">{{ $case['focus'] }}</span>
                        <span class="mt-2 block font-semibold text-slate-900">{{ $case['name'] }}</span>
                        <span class="mt-1 block text-sm text-slate-500">Age {{ $case['age'] }} · Fictional client</span>
                    </button>
                @endforeach
            </section>

            <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                <p class="text-sm font-semibold text-blue-700">PRACTICE CASE</p>
                <h2 class="mt-2 text-2xl font-semibold text-slate-900">{{ $activeCase['focus'] }}</h2>
                <p class="mt-4 rounded-2xl bg-slate-50 p-5 text-sm leading-7 text-slate-700">{{ $activeCase['summary'] }}</p>
                <form wire:submit="savePracticeNote" class="mt-6 space-y-3">
                    <label for="practice-note" class="block text-sm font-medium text-slate-700">Simulation note</label>
                    <textarea id="practice-note" wire:model="practiceNote" rows="7" maxlength="3000" class="block w-full rounded-xl border-slate-300 text-sm focus:border-blue-600 focus:ring-blue-600" placeholder="Practice a respectful, factual observation or reflection. Do not enter real patient information."></textarea>
                    @error('practiceNote') <p class="text-sm text-rose-600">{{ $message }}</p> @enderror
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <p class="text-xs text-slate-500">Saved to this browser session only. These notes are not clinic records.</p>
                        <button type="submit" class="rounded-xl bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-800">Save simulation note</button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</div>
