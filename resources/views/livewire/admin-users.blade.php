<div class="min-h-screen bg-slate-50 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl space-y-6">
        <header class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <a href="{{ route('dashboard') }}" class="text-sm font-medium text-blue-700 hover:text-blue-900">← Dashboard</a>
                <p class="mt-5 text-sm font-semibold text-blue-700">ADMIN WORKSPACE</p>
                <h1 class="mt-1 text-3xl font-semibold tracking-tight text-slate-900">Accounts and applications</h1>
                <p class="mt-2 text-sm text-slate-500">{{ $pendingCount }} application(s) awaiting review.</p>
            </div>
        </header>

        @if (session()->has('status'))
            <p class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">{{ session('status') }}</p>
        @endif
        @error('approval')
            <p class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900" role="alert">{{ $message }}</p>
        @enderror

        @if ($editingUserId !== null)
            <section class="rounded-2xl border border-blue-100 bg-white p-5 shadow-sm sm:p-7">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">Edit account</h2>
                        <p class="mt-1 text-sm text-slate-500">Personal information and account role.</p>
                    </div>
                    <button type="button" wire:click="cancelEdit" class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Close</button>
                </div>
                <form wire:submit="saveUser" class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <label class="text-sm font-medium text-slate-700">First name
                        <input wire:model="firstName" required class="mt-1 block w-full rounded-xl border-slate-300 text-sm">
                        @error('firstName') <small class="text-rose-600">{{ $message }}</small> @enderror
                    </label>
                    <label class="text-sm font-medium text-slate-700">Middle name
                        <input wire:model="middleName" class="mt-1 block w-full rounded-xl border-slate-300 text-sm">
                        @error('middleName') <small class="text-rose-600">{{ $message }}</small> @enderror
                    </label>
                    <label class="text-sm font-medium text-slate-700">Last name
                        <input wire:model="lastName" required class="mt-1 block w-full rounded-xl border-slate-300 text-sm">
                        @error('lastName') <small class="text-rose-600">{{ $message }}</small> @enderror
                    </label>
                    <label class="text-sm font-medium text-slate-700">Email
                        <input wire:model="email" type="email" required class="mt-1 block w-full rounded-xl border-slate-300 text-sm">
                        @error('email') <small class="text-rose-600">{{ $message }}</small> @enderror
                    </label>
                    <label class="text-sm font-medium text-slate-700">Role
                        <select wire:model.live="role" class="mt-1 block w-full rounded-xl border-slate-300 text-sm">
                            <option value="clinic_staff">Clinic Staff</option>
                            <option value="admin">Admin</option>
                        </select>
                        @error('role') <small class="text-rose-600">{{ $message }}</small> @enderror
                    </label>
                    <div class="flex items-end gap-3">
                        <button type="submit" class="rounded-xl bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-800">Save account</button>
                        <button type="button" wire:click="cancelEdit" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700">Cancel</button>
                    </div>
                </form>
            </section>
        @endif

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="grid grid-cols-1 gap-3 border-b border-slate-200 p-4 sm:grid-cols-3">
                <input wire:model.live.debounce.300ms="search" type="search" placeholder="Search name or email" class="rounded-xl border-slate-300 text-sm">
                <select wire:model.live="roleFilter" class="rounded-xl border-slate-300 text-sm">
                    <option value="">All roles</option>
                    <option value="clinic_staff">Clinic Staff</option>
                    <option value="admin">Admin</option>
                </select>
                <select wire:model.live="statusFilter" class="rounded-xl border-slate-300 text-sm">
                    <option value="">All statuses</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                        <tr><th class="px-5 py-3">Account</th><th class="px-5 py-3">Role</th><th class="px-5 py-3">Access status</th><th class="px-5 py-3">Actions</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($accounts as $account)
                            <tr wire:key="account-{{ $account->id }}" class="align-top">
                                <td class="px-5 py-4">
                                    <p class="font-semibold text-slate-900">{{ $account->name }}</p>
                                    <p class="mt-1 text-slate-500">{{ $account->email }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="font-medium text-slate-800">{{ $account->roleLabel() }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <span @class([
                                        'rounded-full px-2.5 py-1 text-xs font-semibold',
                                        'bg-amber-50 text-amber-800' => $account->approval_status === 'pending',
                                        'bg-emerald-50 text-emerald-800' => $account->approval_status === 'approved' && $account->is_active,
                                        'bg-rose-50 text-rose-800' => $account->approval_status === 'rejected' || ! $account->is_active,
                                    ])>{{ $account->approval_status !== 'approved' ? str($account->approval_status)->title() : ($account->is_active ? 'Approved' : 'Inactive') }}</span>
                                </td>
                                <td class="min-w-64 px-5 py-4">
                                    <div class="flex flex-wrap gap-2">
                                        <button type="button" wire:click="editUser({{ $account->id }})" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">Edit</button>
                                        @if ($account->approval_status === 'pending' && $account->role === 'clinic_staff')
                                            <button type="button" wire:click="acceptApplication({{ $account->id }})" class="rounded-lg bg-emerald-700 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-800">Accept</button>
                                            <button type="button" wire:click="rejectApplication({{ $account->id }})" class="rounded-lg bg-rose-700 px-3 py-1.5 text-xs font-semibold text-white hover:bg-rose-800">Reject</button>
                                        @elseif ($account->approval_status === 'approved' && ! $account->is(auth()->user()))
                                            <button type="button" wire:click="toggleAccount({{ $account->id }})" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">{{ $account->is_active ? 'Deactivate' : 'Activate' }}</button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-5 py-12 text-center text-slate-500">No accounts match these filters.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-200 px-5 py-4">{{ $accounts->links() }}</div>
        </section>
    </div>
</div>
