<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Component;

class WorkflowAssistant extends Component
{
    public string $question = '';

    public string $answer = '';

    public ?string $destinationRoute = null;

    public function mount(): void
    {
        $this->authorizeWorkspace();
    }

    public function ask(): void
    {
        $this->authorizeWorkspace();

        $this->question = Str::squish($this->question);
        $validated = $this->validate([
            'question' => ['required', 'string', 'max:200'],
        ]);
        $destination = $this->destinationFor(Str::lower($validated['question']));

        if ($destination === null) {
            $this->answer = auth()->user()->isAdmin()
                ? 'Try asking about account approvals, staff accounts, or the clinic overview.'
                : 'Try asking about patient check-in, patient records, appointments and queue, or prescription records.';
            $this->destinationRoute = null;

            return;
        }

        $this->answer = $destination['answer'];
        $this->destinationRoute = $destination['route'];
    }

    public function askSuggestedQuestion(string $question): void
    {
        $this->question = $question;
        $this->ask();
    }

    /**
     * @return array{answer: string, route: string, label: string}|null
     */
    private function destinationFor(string $question): ?array
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return match (true) {
                Str::contains($question, ['account', 'application', 'approve', 'staff']) => [
                    'answer' => 'Review applications and manage authorized staff accounts in User accounts.',
                    'route' => 'admin.accounts',
                    'label' => 'User accounts',
                ],
                Str::contains($question, ['overview', 'dashboard', 'clinic']) => [
                    'answer' => 'The Clinic overview shows aggregate activity and account shortcuts.',
                    'route' => 'dashboard',
                    'label' => 'Clinic overview',
                ],
                default => null,
            };
        }

        return match (true) {
            Str::contains($question, ['check-in', 'check in', 'qr', 'review']) => [
                'answer' => 'Review submitted patient details before registering a patient in the queue.',
                'route' => 'check-ins.index',
                'label' => 'Patient check-in',
            ],
            Str::contains($question, ['appointment', 'schedule', 'queue', 'wait']) => [
                'answer' => 'Open the appointment and queue workspace to schedule visits and monitor the line.',
                'route' => 'appointments',
                'label' => 'Appointments and queue',
            ],
            Str::contains($question, ['prescription', 'medicine', 'medication']) => [
                'answer' => 'Create and review patient-linked prescription records in this workspace.',
                'route' => 'prescriptions',
                'label' => 'Prescription records',
            ],
            Str::contains($question, ['patient', 'record', 'search', 'register', 'profile']) => [
                'answer' => 'Search patient records, register a patient, or open a full patient profile here.',
                'route' => 'patients',
                'label' => 'Patient records',
            ],
            Str::contains($question, ['overview', 'dashboard']) => [
                'answer' => 'The clinic overview shows check-ins, queue activity, and workflow shortcuts.',
                'route' => 'dashboard',
                'label' => 'Clinic overview',
            ],
            default => null,
        };
    }

    private function authorizeWorkspace(): void
    {
        $user = auth()->user();

        abort_unless(
            $user instanceof User
                && $user->hasWorkspaceAccess()
                && ($user->isClinicStaff() || $user->isAdmin()),
            403,
        );
    }

    public function render(): View
    {
        $this->authorizeWorkspace();

        $destinations = auth()->user()->isAdmin()
            ? [
                'admin.accounts' => ['url' => route('admin.accounts'), 'label' => 'User accounts'],
                'dashboard' => ['url' => route('dashboard'), 'label' => 'Clinic overview'],
            ]
            : [
                'check-ins.index' => ['url' => route('check-ins.index'), 'label' => 'Patient check-in'],
                'appointments' => ['url' => route('appointments'), 'label' => 'Appointments and queue'],
                'prescriptions' => ['url' => route('prescriptions'), 'label' => 'Prescription records'],
                'patients' => ['url' => route('patients'), 'label' => 'Patient records'],
                'dashboard' => ['url' => route('dashboard'), 'label' => 'Clinic overview'],
            ];

        return view('livewire.workflow-assistant', [
            'destination' => $destinations[$this->destinationRoute] ?? null,
        ]);
    }
}
