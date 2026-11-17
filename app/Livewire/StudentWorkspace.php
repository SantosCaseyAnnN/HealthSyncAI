<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class StudentWorkspace extends Component
{
    public string $selectedCase = 'case-a';

    public string $practiceNote = '';

    public function boot(): void
    {
        abort_unless(auth()->user()?->hasWorkspaceAccess() && auth()->user()->isStudent(), 403);
    }

    public function updatedSelectedCase(): void
    {
        $this->practiceNote = session()->get('student-practice-notes', [])[$this->selectedCase] ?? '';
    }

    public function savePracticeNote(): void
    {
        $this->validate([
            'selectedCase' => ['required', 'in:case-a,case-b,case-c'],
            'practiceNote' => ['required', 'string', 'max:3000'],
        ]);

        $notes = session()->get('student-practice-notes', []);
        $notes[$this->selectedCase] = $this->practiceNote;
        session()->put('student-practice-notes', $notes);
        session()->flash('status', 'Your simulation note was saved in this session.');
    }

    public function render(): View
    {
        $cases = $this->cases();

        return view('livewire.student-workspace', [
            'cases' => $cases,
            'activeCase' => collect($cases)->firstWhere('id', $this->selectedCase),
        ]);
    }

    /**
     * @return array<int, array{id: string, name: string, age: int, focus: string, summary: string}>
     */
    private function cases(): array
    {
        return [
            [
                'id' => 'case-a',
                'name' => 'Jordan Demo',
                'age' => 28,
                'focus' => 'Mobility assessment',
                'summary' => 'A fictional client reports stiffness after a long desk shift. Practice documenting a safe, non-diagnostic movement assessment.',
            ],
            [
                'id' => 'case-b',
                'name' => 'Casey Sample',
                'age' => 46,
                'focus' => 'Communication practice',
                'summary' => 'A fictional client is preparing for a routine follow-up. Practice open-ended questions and clear patient education.',
            ],
            [
                'id' => 'case-c',
                'name' => 'Taylor Fiction',
                'age' => 19,
                'focus' => 'Vital-signs workflow',
                'summary' => 'A fictional client is attending a skills lab. Practice describing the measurement workflow; no real measurements are provided.',
            ],
        ];
    }
}
