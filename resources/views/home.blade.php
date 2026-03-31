<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Messenger') }}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap">
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    </head>
    <body class="text-white" style="background: #020617; font-family: 'Manrope', sans-serif;">
        <div class="min-h-screen" style="background: radial-gradient(circle at top, rgba(56, 189, 248, 0.2), transparent 35%), linear-gradient(180deg, #020617 0%, #0f172a 55%, #111827 100%);">
            <header class="mx-auto flex max-w-6xl items-center justify-between px-6 py-6">
                <a href="{{ route('home') }}" class="text-xl font-semibold tracking-wide">
                    {{ config('app.name', 'Messenger') }}
                </a>

                <nav class="flex items-center gap-3 text-sm">
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-full px-4 py-2 transition" style="border: 1px solid rgba(56, 189, 248, 0.4); color: #e0f2fe;">
                            Dashboard
                        </a>
                        <a href="{{ route('messenger') }}" class="rounded-full px-4 py-2 font-semibold transition" style="background: #38bdf8; color: #0f172a;">
                            Open Messenger
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-full px-4 py-2 transition" style="border: 1px solid rgba(255, 255, 255, 0.15);">
                            Log in
                        </a>
                        <a href="{{ route('register') }}" class="rounded-full px-4 py-2 font-semibold transition" style="background: #38bdf8; color: #0f172a;">
                            Create account
                        </a>
                    @endauth
                </nav>
            </header>

            <main class="mx-auto grid max-w-6xl gap-10 px-6 pb-20 pt-10 lg:grid-cols-[1.1fr_0.9fr] lg:items-center lg:pt-20">
                <section class="space-y-8">
                    <span class="inline-flex rounded-full px-4 py-1 text-sm" style="border: 1px solid rgba(56, 189, 248, 0.3); background: rgba(56, 189, 248, 0.1); color: #e0f2fe;">
                        Portfolio-ready messaging experience
                    </span>

                    <div class="space-y-4">
                        <h1 class="max-w-3xl text-4xl font-black leading-tight text-white md:text-6xl">
                            A polished Laravel messenger built for clean demos and reliable navigation.
                        </h1>
                        <p class="max-w-2xl text-lg leading-8" style="color: #cbd5e1;">
                            Browse a simple dashboard, open the messenger workspace, and move through the product without dead links, broken images, or confusing UI states.
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-4">
                        @auth
                            <a href="{{ route('messenger') }}" class="rounded-2xl px-6 py-3 font-semibold transition" style="background: #38bdf8; color: #0f172a; box-shadow: 0 16px 35px rgba(56, 189, 248, 0.2);">
                                Launch Messenger
                            </a>
                            <a href="{{ route('dashboard') }}" class="rounded-2xl px-6 py-3 font-semibold transition" style="border: 1px solid rgba(255, 255, 255, 0.15);">
                                View Dashboard
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="rounded-2xl px-6 py-3 font-semibold transition" style="background: #38bdf8; color: #0f172a; box-shadow: 0 16px 35px rgba(56, 189, 248, 0.2);">
                                Start Demo
                            </a>
                            <a href="{{ route('login') }}" class="rounded-2xl px-6 py-3 font-semibold transition" style="border: 1px solid rgba(255, 255, 255, 0.15);">
                                Log In
                            </a>
                        @endauth
                    </div>
                </section>

                <section class="rounded-3xl p-6 shadow-2xl" style="border: 1px solid rgba(255, 255, 255, 0.1); background: rgba(255, 255, 255, 0.05);">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm uppercase tracking-widest" style="color: rgba(186, 230, 253, 0.8);">Highlights</p>
                                <h2 class="mt-2 text-2xl font-bold text-white">What this demo focuses on</h2>
                            </div>
                            <div class="rounded-2xl px-4 py-2 text-sm font-semibold" style="background: rgba(74, 222, 128, 0.15); color: #dcfce7;">
                                Presentation Safe
                            </div>
                        </div>

                        <div class="grid gap-4 text-sm" style="color: #e2e8f0;">
                            <div class="rounded-2xl p-4" style="border: 1px solid rgba(255, 255, 255, 0.1); background: rgba(15, 23, 42, 0.5);">
                                Clear page structure with dedicated routes for home, dashboard, and messenger.
                            </div>
                            <div class="rounded-2xl p-4" style="border: 1px solid rgba(255, 255, 255, 0.1); background: rgba(15, 23, 42, 0.5);">
                                Auth pages remain accessible and consistent with the main navigation flow.
                            </div>
                            <div class="rounded-2xl p-4" style="border: 1px solid rgba(255, 255, 255, 0.1); background: rgba(15, 23, 42, 0.5);">
                                Messenger UI avoids broken placeholder assets and dead interaction points.
                            </div>
                        </div>
                    </div>
                </section>
            </main>
        </div>
    </body>
</html>
