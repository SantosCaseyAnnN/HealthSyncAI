<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>HealthSyncAI - Login</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>

<body class="auth-page">

    @if (session('status'))
        <div class="fixed right-6 top-6 z-50 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 shadow-lg shadow-emerald-200/40">
            {{ session('status') }}
        </div>
    @endif

    <div class="auth-container">

        {{-- LEFT GRADIENT AREA --}}
        <div class="auth-gradient"></div>


        {{-- LOGIN FORM AREA --}}
        <div class="auth-form-area">

            <div class="auth-card">

                <form wire:submit="login">

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


                    {{-- LOGIN BUTTON --}}
                    <button
                        type="submit"
                        class="auth-button"
                    >
                        Log In
                    </button>


                    {{-- FORGOT PASSWORD --}}
                    <div class="auth-link">

                        <a href="#">
                            Forgot password?
                        </a>

                    </div>


                    {{-- REGISTER LINK --}}
                    <div class="auth-link register-link">

                        <span>No Account?</span>

                        <a href="{{ route('register') }}">
                            Sign Up Here
                        </a>

                    </div>

                </form>

            </div>

        </div>

    </div>

    @livewireScripts

</body>
</html>