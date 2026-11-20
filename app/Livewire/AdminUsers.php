<?php

namespace App\Livewire;

use App\Models\User;
use App\Notifications\AccountDecisionNotification;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class AdminUsers extends Component
{
    use WithPagination;

    public string $search = '';

    public string $roleFilter = '';

    public string $statusFilter = '';

    public ?int $editingUserId = null;

    public string $firstName = '';

    public string $middleName = '';

    public string $lastName = '';

    public string $email = '';

    public string $role = User::ROLE_CLINIC_STAFF;

    public function boot(): void
    {
        abort_unless(auth()->user()?->hasWorkspaceAccess() && auth()->user()->isAdmin(), 403);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedRoleFilter(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function editUser(int $userId): void
    {
        $user = User::query()->findOrFail($userId);
        $this->editingUserId = $user->id;
        $this->firstName = $user->first_name ?? '';
        $this->middleName = $user->middle_name ?? '';
        $this->lastName = $user->last_name ?? '';
        $this->email = $user->email;
        $this->role = $user->role;
    }

    public function saveUser(): void
    {
        $user = User::query()->findOrFail($this->editingUserId);

        if ($user->is($this->getCurrentUser()) && $this->role !== $user->role) {
            abort(403);
        }

        $validated = $this->validate([
            'firstName' => ['required', 'string', 'max:100'],
            'middleName' => ['nullable', 'string', 'max:100'],
            'lastName' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::in([
                User::ROLE_CLINIC_STAFF,
                User::ROLE_ADMIN,
            ])],
        ]);
        $roleChanged = $user->role !== $validated['role'];
        $requiresReview = $roleChanged && $validated['role'] === User::ROLE_CLINIC_STAFF && $user->role === User::ROLE_ADMIN;

        $attributes = [
            'first_name' => $validated['firstName'],
            'middle_name' => $validated['middleName'] ?: null,
            'last_name' => $validated['lastName'],
            'name' => trim($validated['firstName'].' '.($validated['middleName'] ?? '').' '.$validated['lastName']),
            'email' => $validated['email'],
            'role' => $validated['role'],
        ];

        if ($requiresReview) {
            $attributes['approval_status'] = User::APPROVAL_PENDING;
            $attributes['is_active'] = false;
        }

        $user->forceFill($attributes)->save();

        session()->flash('status', 'Account details were updated.');
        $this->cancelEdit();
    }

    public function acceptApplication(int $userId): void
    {
        $user = User::query()->findOrFail($userId);
        abort_unless(
            $user->role === User::ROLE_CLINIC_STAFF
                && $user->approval_status === User::APPROVAL_PENDING,
            422,
        );

        $user->forceFill([
            'approval_status' => User::APPROVAL_APPROVED,
            'is_active' => true,
        ])->save();

        $user->notify(new AccountDecisionNotification(
            'accepted',
            'Your HealthSyncAI application was accepted. Your approved workspace is now available.',
        ));
        session()->flash('status', 'Application accepted and applicant notified.');
    }

    public function rejectApplication(int $userId): void
    {
        $user = User::query()->findOrFail($userId);
        abort_unless(
            $user->role === User::ROLE_CLINIC_STAFF
                && $user->approval_status === User::APPROVAL_PENDING,
            422,
        );

        $user->forceFill([
            'approval_status' => User::APPROVAL_REJECTED,
            'is_active' => false,
        ])->save();

        $user->notify(new AccountDecisionNotification(
            'rejected',
            'Your HealthSyncAI application was not accepted. Contact the clinic administrator if you believe this decision was made in error.',
        ));
        session()->flash('status', 'Application rejected and applicant notified.');
    }

    public function toggleAccount(int $userId): void
    {
        $user = User::query()->findOrFail($userId);
        abort_unless(! $user->is($this->getCurrentUser()), 403);
        abort_unless($user->approval_status === User::APPROVAL_APPROVED, 422);

        $user->forceFill(['is_active' => ! $user->is_active])->save();
        session()->flash('status', $user->is_active ? 'Account activated.' : 'Account deactivated.');
    }

    public function cancelEdit(): void
    {
        $this->reset(
            'editingUserId',
            'firstName',
            'middleName',
            'lastName',
            'email',
        );
        $this->role = User::ROLE_CLINIC_STAFF;
        $this->resetValidation();
    }

    public function render(): View
    {
        $search = '%'.$this->search.'%';

        return view('livewire.admin-users', [
            'accounts' => User::query()
                ->when($this->roleFilter !== '', fn ($query) => $query->where('role', $this->roleFilter))
                ->when($this->statusFilter !== '', function ($query) {
                    if ($this->statusFilter === 'inactive') {
                        $query->where('is_active', false);
                    } else {
                        $query->where('approval_status', $this->statusFilter);
                    }
                })
                ->when($this->search !== '', fn ($query) => $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', $search)
                        ->orWhere('email', 'like', $search);
                }))
                ->orderByRaw("CASE WHEN approval_status = 'pending' THEN 0 ELSE 1 END")
                ->orderBy('name')
                ->paginate(15),
            'pendingCount' => User::query()
                ->where('role', User::ROLE_CLINIC_STAFF)
                ->where('approval_status', User::APPROVAL_PENDING)
                ->count(),
        ]);
    }

    private function getCurrentUser(): User
    {
        return auth()->user();
    }
}
