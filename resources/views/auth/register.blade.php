@extends('layouts.guest')

@section('title', 'Create account')

@section('content')
    <div class="mx-auto w-full max-w-lg text-center">
        <div class="section-shell relative overflow-hidden">
            <div class="absolute inset-x-6 top-0 h-px bg-gradient-to-r from-transparent via-white/10 to-transparent"></div>
            <div class="absolute -top-24 right-6 h-52 w-52 rounded-full bg-violet-400/10 blur-3xl"></div>
            <div class="absolute -bottom-16 left-4 h-48 w-48 rounded-full bg-sky-400/10 blur-3xl"></div>

            <div class="relative">
                <div class="badge-soft w-fit">Create account</div>
                <h1 class="mt-4 text-3xl font-semibold text-ink">Turn every post into a polished thread</h1>
                <p class="mt-3 text-sm text-muted">Save your long-form writing once and transform it into ready-to-share social posts whenever inspiration strikes.</p>
            </div>

            <form method="POST" action="{{ url('/register') }}" class="relative mt-8 space-y-5 text-left">
                @csrf

                <div>
                    <label for="name" class="text-xs font-semibold uppercase tracking-wide text-soft">Name</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus
                           class="mt-2 input-field w-full" placeholder="Jules Carter" />
                </div>

                <div>
                    <label for="email" class="text-xs font-semibold uppercase tracking-wide text-soft">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required
                           class="mt-2 input-field w-full" placeholder="you@remixer.studio" />
                </div>

                <div>
                    <label for="password" class="text-xs font-semibold uppercase tracking-wide text-soft">Password</label>
                    <input id="password" name="password" type="password" required
                           class="mt-2 input-field w-full" placeholder="Create a password" />
                </div>

                <div>
                    <label for="password_confirmation" class="text-xs font-semibold uppercase tracking-wide text-soft">Confirm password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required
                           class="mt-2 input-field w-full" placeholder="Retype your password" />
                </div>

                <button type="submit" class="pill-button--primary w-full justify-center">
                    Create account
                </button>
            </form>

            <p class="mt-8 text-center text-sm text-muted">
                Already have an account?
                <a href="{{ route('login') }}" class="font-semibold text-soft hover:text-ink">Log in</a>
            </p>
        </div>
    </div>
@endsection

