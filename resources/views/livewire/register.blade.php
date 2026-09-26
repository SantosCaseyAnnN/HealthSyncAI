<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>HealthSyncAI - Sign Up</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>

<body class="auth-page">

    <div class="auth-container">

        {{-- LEFT GRADIENT AREA --}}
        <div class="auth-gradient"></div>


        {{-- SIGN UP FORM AREA --}}
        <div class="auth-form-area">

            <div class="auth-card signup-card">

                <form wire:submit="register">

                    {{-- FULL NAME --}}
                    <div class="form-group">

                        <label for="full_name">
                            Full Name
                        </label>

                        <input
                            type="text"
                            id="full_name"
                            wire:model="full_name"
                            placeholder="Value"
                        >

                    </div>


                    {{-- USER ID --}}
                    <div class="form-group">

                        <label for="user_id">
                            User ID
                        </label>

                        <input
                            type="text"
                            id="user_id"
                            wire:model="user_id"
                            placeholder="Value"
                        >

                    </div>


                    {{-- EMAIL --}}
                    <div class="form-group">

                        <label for="email">
                            Email
                        </label>

                        <input
                            type="email"
                            id="email"
                            wire:model="email"
                            placeholder="Value"
                        >

                    </div>


                    {{-- PASSWORD --}}
                    <div class="form-group">

                        <label for="password">
                            Password
                        </label>

                        <input
                            type="password"
                            id="password"
                            wire:model="password"
                            placeholder="Value"
                        >

                    </div>


                    {{-- TERMS --}}
                    <div class="terms">

                        <input
                            type="checkbox"
                            id="terms"
                            wire:model="terms"
                        >

                        <label for="terms">
                            I Agree to the Terms of Service
                            <br>
                            and Privacy Policy
                        </label>

                    </div>


                    {{-- SIGN UP BUTTON --}}
                    <button
                        type="submit"
                        class="auth-button"
                    >
                        Sign Up
                    </button>


                    {{-- LOGIN LINK --}}
                    <div class="auth-link login-link">

                        <span>Already have an account?</span>

                        <a href="{{ route('login') }}">
                            Log In Here
                        </a>

                    </div>

                </form>

            </div>

        </div>

    </div>

    @livewireScripts

</body>
</html>