<section class="workflow-assistant" aria-labelledby="workflow-assistant-title">
    <div class="workflow-assistant-heading">
        <span class="workflow-assistant-mark" aria-hidden="true">✦</span>
        <div>
            <p>HealthSyncAI assistant</p>
            <h2 id="workflow-assistant-title">Not sure where to go?</h2>
            <small>Ask about a task and I’ll point you to the right workspace.</small>
        </section>
    </div>

    <form class="workflow-assistant-form" wire:submit="ask">
        <label class="sr-only" for="workflow-assistant-question">What do you need help finding?</label>
        <input
            id="workflow-assistant-question"
            type="text"
            wire:model="question"
            maxlength="200"
            placeholder="e.g. Where do I review patient check-ins?"
        >
        <button type="submit">Ask</button>
    </form>

    @error('question')
        <p class="workflow-assistant-error">{{ $message }}</p>
    @enderror

    @if (auth()->user()->isAdmin())
        <div class="workflow-assistant-suggestions" aria-label="Suggested questions">
            <button type="button" wire:click="askSuggestedQuestion('How do I approve an account?')">Approve an account</button>
            <button type="button" wire:click="askSuggestedQuestion('Where is the clinic overview?')">Clinic overview</button>
        </div>
    @else
        <div class="workflow-assistant-suggestions" aria-label="Suggested questions">
            <button type="button" wire:click="askSuggestedQuestion('Where do I review patient check-ins?')">Patient check-in</button>
            <button type="button" wire:click="askSuggestedQuestion('Where do I find appointments and queue?')">Appointments &amp; queue</button>
            <button type="button" wire:click="askSuggestedQuestion('Where can I search patient records?')">Patient records</button>
            <button type="button" wire:click="askSuggestedQuestion('Where can I find prescriptions?')">Prescription records</button>
        </div>
    @endif

    @if ($answer !== '')
        <div class="workflow-assistant-answer" role="status">
            <p>{{ $answer }}</p>
            @if ($destination)
                <a href="{{ $destination['url'] }}">Open {{ $destination['label'] }} <span aria-hidden="true">→</span></a>
            @endif
        </div>
    @endif

    <p class="workflow-assistant-note">Navigation help only. This assistant does not provide medical advice.</p>
</div>
