<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name', 'Remixer'))</title>

        @php
            $useVite = ! app()->runningUnitTests() && file_exists(public_path('build/manifest.json'));
        @endphp

        @if ($useVite)
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="flex min-h-screen items-center justify-center px-4 py-12">
        <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_top,var(--mist)_0,transparent_60%)]"></div>

        @if (session('status'))
            <div class="alert-soft fixed top-6 left-1/2 z-50 -translate-x-1/2 rounded-2xl px-5 py-3 text-sm">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert-error fixed top-6 left-1/2 z-50 -translate-x-1/2 rounded-2xl px-5 py-3 text-sm">
                <ul class="list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </body>
</html>

