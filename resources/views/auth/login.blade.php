@extends('layouts.guest')

@section('title', 'Log in')

@section('content')
    <div class="mx-auto w-full max-w-lg text-center">
        <div class="section-shell relative overflow-hidden">
            <div class="absolute inset-x-6 top-0 h-px bg-gradient-to-r from-transparent via-white/10 to-transparent"></div>
            <div class="absolute -top-24 right-10 h-48 w-48 rounded-full bg-sky-400/10 blur-3xl"></div>
            <div class="absolute -bottom-16 left-10 h-52 w-52 rounded-full bg-emerald-400/10 blur-3xl"></div>

            <div class="relative">
                <div class="badge-soft w-fit">Welcome back</div>
                <h1 class="mt-4 text-3xl font-semibold text-ink">Sign in to keep the remix going</h1>
                <p class="mt-3 text-sm text-muted">Access your saved posts, pending tweets, and posting checklist in one sleek workspace.</p>
            </div>

            <form method="POST" action="{{ url('/login') }}" class="relative mt-8 space-y-5 text-left">
                @csrf

                <div>
                    <label for="email" class="text-xs font-semibold uppercase tracking-wide text-soft">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                           class="mt-2 input-field w-full" placeholder="you@remixer.studio" />
                </div>

                <div>
                    <label for="password" class="text-xs font-semibold uppercase tracking-wide text-soft">Password</label>
                    <input id="password" name="password" type="password" required
                           class="mt-2 input-field w-full" placeholder="••••••••" />
                </div>

                <div class="flex items-center justify-between text-sm text-muted">
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" name="remember" class="rounded border-white/30 bg-white/60 text-soft focus:ring-0" />
                        <span class="text-muted">Remember me</span>
                    </label>

                    <a href="{{ route('register') }}" class="font-semibold text-soft hover:text-ink">Need an account?</a>
                </div>

                <button type="submit" class="pill-button--primary w-full justify-center">
                    Log in
                </button>
            </form>
        </div>
    </div>
@endsection

