<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, mixed>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'role' => ['required', Rule::in([
                User::ROLE_CLINIC_STAFF,
                User::ROLE_STUDENT,
                User::ROLE_ADMIN,
            ])],
            'school_id' => [
                'exclude_unless:role,'.User::ROLE_STUDENT,
                'required',
                'string',
                'max:100',
                'unique:users,school_id',
            ],
            'year_level' => [
                'exclude_unless:role,'.User::ROLE_STUDENT,
                'required',
                Rule::in(['First', 'Second', 'Third', 'Fourth', 'Fifth', 'Higher', 'Graduate/Postgrad']),
            ],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        return User::create([
            'name' => trim($input['first_name'].' '.($input['middle_name'] ?? '').' '.$input['last_name']),
            'first_name' => $input['first_name'],
            'middle_name' => $input['middle_name'] ?? null,
            'last_name' => $input['last_name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'role' => $input['role'],
            'approval_status' => User::APPROVAL_PENDING,
            'is_active' => false,
            'school_id' => $input['school_id'] ?? null,
            'year_level' => $input['year_level'] ?? null,
        ]);
    }
}
