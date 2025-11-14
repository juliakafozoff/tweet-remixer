<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Remixer') }}</title>

        @php
            $useVite = ! app()->runningUnitTests() && file_exists(public_path('build/manifest.json'));
        @endphp

        @if ($useVite)
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="font-sans">
        <div class="relative min-h-screen overflow-x-hidden">
            <header class="sticky top-0 z-50">
                <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8 glass-panel rounded-full">
                    <a href="{{ route('dashboard') }}" class="group flex items-center gap-3 text-lg font-semibold text-ink">
                        <span class="shimmer-border inline-flex h-10 w-10 items-center justify-center rounded-full bg-white/70 font-bold text-ink">
                            R
                        </span>
                        <span class="tracking-tight transition duration-200 group-hover:text-soft">Remixer</span>
                    </a>

                    <nav class="flex items-center gap-3 text-sm font-medium text-muted">
                        <a href="{{ route('posts.create') }}" class="pill-button--primary gap-1 hidden sm:inline-flex">
                            <svg style="width:0.9rem;height:0.9rem" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M12 5v14m7-7H5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            New Post
                        </a>

                        <a href="{{ route('posts.create') }}" class="pill-button--ghost sm:hidden">
                            New
                        </a>

                        <a href="{{ route('billing.show') }}" class="pill-button--ghost">
                            Billing
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="pill-button--outline">
                                Logout
                            </button>
                        </form>
                    </nav>
                </div>
            </header>

            <main class="page-grid">
                @if (session('status'))
                    <div class="alert-soft glass-panel rounded-3xl px-6 py-4 text-sm">
                        <div class="flex items-center justify-center gap-3 font-medium">
                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-white/60 text-ink">✓</span>
                            <span>{{ session('status') }}</span>
                        </div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert-error glass-panel rounded-3xl px-6 py-4 text-sm">
                        <div class="flex items-start justify-center gap-3">
                            <span class="mt-0.5 inline-flex h-6 w-6 items-center justify-center rounded-full bg-white/60 font-semibold text-ink">!</span>
                            <div>
                                <p class="font-semibold">We hit a snag:</p>
                                <ul class="mt-2 list-disc space-y-1 pl-5 text-soft">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>

        @stack('scripts')
    </body>
</html>

