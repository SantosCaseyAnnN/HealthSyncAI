<?php

namespace App\Livewire;

use Livewire\Component;

class DashboardStats extends Component
{
    public bool $showAssistant = true;

    public string $question = '';

    public string $assistantResponse = '';

    public function toggleAssistant()
    {
        $this->showAssistant = !$this->showAssistant;
    }

    public function askQuestion()
    {
        $question = strtolower(trim($this->question));

        if ($question === '') {
            return;
        }

        if (
            str_contains($question, 'register') ||
            str_contains($question, 'patient')
        ) {
            $this->assistantResponse =
                'To register a patient, select "Register Patient" from Quick Access or the button at the top of the dashboard.';
        }

        elseif (
            str_contains($question, 'appointment') ||
            str_contains($question, 'schedule')
        ) {
            $this->assistantResponse =
                'You can view today’s appointments from the dashboard or schedule a new appointment through Quick Access.';
        }

        elseif (
            str_contains($question, 'laboratory') ||
            str_contains($question, 'lab')
        ) {
            $this->assistantResponse =
                'You can create a laboratory request through Quick Access. Pending laboratory requests are shown on the right side of the dashboard.';
        }

        elseif (
            str_contains($question, 'medical') ||
            str_contains($question, 'record')
        ) {
            $this->assistantResponse =
                'Medical records can be accessed through the Medical Records section in the navigation menu.';
        }

        else {
            $this->assistantResponse =
                'I can help you navigate HealthSyncAI. Try asking about patients, appointments, laboratory requests, or medical records.';
        }

        $this->question = '';
    }

    public function quickQuestion($question)
    {
        $this->question = $question;

        $this->askQuestion();
    }

    public function render()
    {
        return view('livewire.dashboard-stats');
    }
}