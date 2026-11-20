<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Login | Agusan National High School Enrollment</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        .captcha-answer-correct {
            border-color: #16a34a !important;
            box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.18) !important;
        }

        .captcha-answer-incorrect {
            border-color: #dc2626 !important;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.18) !important;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="min-h-screen bg-gray-100" x-data="{ mobileMenuOpen: false }">

    <header class="fixed top-0 left-0 right-0 w-full bg-white/95 backdrop-blur-sm shadow-sm border-b border-gray-200 z-50">
        <div class="container mx-auto flex min-h-20 items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
            <div class="flex items-center">
                <img src="{{ asset('images/school-logo-light.png') }}" alt="Agusan National High School" class="h-10 w-auto sm:h-12">
            </div>

            <button type="button" class="inline-flex items-center justify-center rounded-md p-2 text-[#0C2C55] hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-[#0C2C55] sm:hidden" @click="mobileMenuOpen = !mobileMenuOpen" :aria-expanded="mobileMenuOpen.toString()" aria-controls="primary-navigation">
                <span class="sr-only">Toggle navigation</span>
                <svg x-show="!mobileMenuOpen" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                <svg x-show="mobileMenuOpen" x-cloak class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>

            <nav id="primary-navigation" :class="mobileMenuOpen ? 'flex' : 'hidden'" class="absolute left-0 right-0 top-full flex-col gap-1 border-b border-gray-200 bg-white px-4 py-4 shadow-lg sm:static sm:flex sm:flex-row sm:items-center sm:gap-6 sm:border-0 sm:bg-transparent sm:p-0 sm:shadow-none lg:gap-10">
                <a @click="mobileMenuOpen = false" href="{{ route('home') }}" class="rounded px-3 py-2 text-sm font-medium uppercase tracking-wide text-[#0C2C55] transition hover:bg-gray-100 hover:text-[#0C2C55]/80 sm:px-0 sm:py-1 sm:hover:bg-transparent sm:text-base">Home</a>
                <a @click="mobileMenuOpen = false" href="{{ route('home') }}#about-us" class="rounded px-3 py-2 text-sm font-medium uppercase tracking-wide text-[#0C2C55] transition hover:bg-gray-100 hover:text-[#0C2C55]/80 sm:px-0 sm:py-1 sm:hover:bg-transparent sm:text-base">About</a>
                <a @click="mobileMenuOpen = false" href="{{ route('home') }}#faq" class="rounded px-3 py-2 text-sm font-medium uppercase tracking-wide text-[#0C2C55] transition hover:bg-gray-100 hover:text-[#0C2C55]/80 sm:px-0 sm:py-1 sm:hover:bg-transparent sm:text-base">FAQ</a>
                <a @click="mobileMenuOpen = false" href="{{ route('home') }}#contact-us" class="rounded px-3 py-2 text-sm font-medium uppercase tracking-wide text-[#0C2C55] transition hover:bg-gray-100 hover:text-[#0C2C55]/80 sm:px-0 sm:py-1 sm:hover:bg-transparent sm:text-base">Contact Us</a>
            </nav>
        </div>
    </header>

    <main class="relative min-h-screen pt-20" style="background-image: url('{{ asset('images/background-blue.jpg') }}'); background-size: cover; background-position: center; background-repeat: no-repeat;">
        <div class="absolute inset-0 bg-gradient-to-r from-[#0C2C55]/90 via-[#0C2C55]/70 to-[#0C2C55]/35"></div>

        <div class="relative container mx-auto flex min-h-[calc(100dvh-5rem)] items-center justify-center px-4 py-10 sm:px-6">
            <div class="grid w-full max-w-6xl gap-8 lg:grid-cols-[1.05fr_0.95fr] lg:items-center lg:gap-10">
                <div class="text-white">
                    <p class="mb-4 text-xs font-semibold uppercase tracking-[0.25em] text-white/80 sm:text-sm sm:tracking-[0.35em]">Staff Portal</p>
                    <h1 class="mb-6 text-3xl font-bold leading-tight sm:text-5xl">
                        Sign in to access the staff portal
                    </h1>
                    <p class="max-w-xl text-base leading-relaxed text-white/85 sm:text-lg">
                        Manage enrollment records, document processing, and school operations securely.
                    </p>
                </div>

                <div class="w-full max-w-md justify-self-center">
                    <div class="rounded-lg bg-white px-5 pb-8 pt-6 shadow-xl sm:px-8 sm:pb-10 sm:pt-8">
                        @if (session('status') === trans('passwords.reset'))
                        <x-password-reset-toasts :include-errors="false" test-prefix="login" />
                        @elseif (session('status'))
                        <div class="mb-4 p-3 rounded bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 text-sm" data-test="login-status">
                            {{ session('status') }}
                        </div>
                        @endif

                        @if (! empty($loginLockoutMinutes))
                        <div class="mb-4 p-3 rounded bg-red-50 border-l-4 border-red-500 text-red-700 text-sm" data-test="login-error">
                            {{ trans('auth.throttle', ['seconds' => $loginLockoutMinutes * 60, 'minutes' => $loginLockoutMinutes]) }}
                        </div>
                        @elseif ($errors->has('username') || $errors->has('password') || $errors->has('captcha_answer'))
                        <div class="mb-4 p-3 rounded bg-red-50 border-l-4 border-red-500 text-red-700 text-sm" data-test="login-error">
                            <p>{{ $errors->first('username') ?: $errors->first('password') ?: $errors->first('captcha_answer') }}</p>
                            @if ($loginAttemptsRemaining !== null)
                            <p class="mt-1 font-semibold" data-test="login-attempts-remaining">
                                {{ trans_choice('auth.attempts_remaining', $loginAttemptsRemaining, ['count' => $loginAttemptsRemaining]) }}
                            </p>
                            @endif
                        </div>
                        @endif

                        <form action="{{ route('login.store') }}" method="POST">
                            @csrf

                            <h1 class="text-3xl font-bold text-blue-800 tracking-tight mb-6">STAFF LOGIN</h1>

                            <div class="mb-6">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="username">
                                    Username
                                </label>
                                <input class="shadow-sm appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                                    id="username"
                                    name="username"
                                    type="text"
                                    placeholder="Enter your username"
                                    value="{{ old('username') }}"
                                    required
                                    autofocus>
                            </div>

                            <div class="mb-3">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                                    Password
                                </label>
                                <div class="relative">
                                    <input class="shadow-sm appearance-none border rounded w-full py-3 px-4 pr-12 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                                        id="password"
                                        name="password"
                                        type="password"
                                        placeholder="*******"
                                        required>
                                    <button type="button" onclick="togglePassword('password')" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700 focus:outline-none">
                                        <svg id="password-eye" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        <svg id="password-eye-slash" class="h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                        </svg>
                                    </button>
                                </div>
                                <div class="mt-2 text-right">
                                    <a href="{{ route('password.request') }}" class="text-sm text-blue-700 font-semibold hover:underline" data-test="forgot-password-link">
                                        Forgot password?
                                    </a>
                                </div>
                            </div>

                            <div class="mb-8">
                                <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                                    <input class="w-14 text-center shadow-sm appearance-none border rounded py-2 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 sm:w-16"
                                        type="text"
                                        value="{{ $captcha_first }}"
                                        readonly>
                                    <span class="text-gray-600 text-lg font-semibold">+</span>
                                    <input class="w-14 text-center shadow-sm appearance-none border rounded py-2 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 sm:w-16"
                                        type="text"
                                        value="{{ $captcha_second }}"
                                        readonly>
                                    <span class="text-gray-600 text-lg font-semibold">=</span>
                                    <input class="w-16 text-center shadow-sm appearance-none border rounded py-2 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 sm:w-20"
                                        id="captcha_answer"
                                        name="captcha_answer"
                                        type="text"
                                        inputmode="numeric"
                                        pattern="\d{1,3}"
                                        maxlength="3"
                                        autocomplete="off"
                                        data-expected-answer="{{ $captcha_first + $captcha_second }}"
                                        required>
                                    <button type="button"
                                        onclick="window.location.reload()"
                                        class="ml-1 inline-flex h-9 w-9 items-center justify-center rounded-full border border-gray-200 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        aria-label="Refresh security check">
                                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 10a6 6 0 10-2.05 4.47M16 10V6m0 4h-4" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <button class="w-full bg-blue-700 hover:bg-blue-800 text-white font-bold py-3 px-4 rounded focus:outline-none focus:ring-4 focus:ring-blue-300 transition duration-300 transform active:scale-95"
                                type="submit">
                                Sign In
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const eyeIcon = document.getElementById(inputId + '-eye');
            const eyeSlashIcon = document.getElementById(inputId + '-eye-slash');

            if (input.type === 'password') {
                input.type = 'text';
                eyeIcon.classList.add('hidden');
                eyeSlashIcon.classList.remove('hidden');
            } else {
                input.type = 'password';
                eyeIcon.classList.remove('hidden');
                eyeSlashIcon.classList.add('hidden');
            }
        }

        const captchaAnswer = document.getElementById('captcha_answer');
        captchaAnswer?.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').slice(0, 3);

            const hasAnswer = this.value !== '';
            const isCorrect = hasAnswer && Number(this.value) === Number(this.dataset.expectedAnswer);

            this.classList.toggle('captcha-answer-correct', isCorrect);
            this.classList.toggle('captcha-answer-incorrect', hasAnswer && !isCorrect);
            this.setAttribute('aria-invalid', String(hasAnswer && !isCorrect));
        });

    </script>
    <x-auth-session-sync />
</body>

</html>
