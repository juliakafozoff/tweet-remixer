<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Remixer') }} | Turn blog posts into ready-to-post tweets</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        @php
            $useVite = file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot'));
        @endphp

        @if ($useVite)
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <style>
                :root {
                    font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
                    color: #6a758a;
                    background: radial-gradient(110% 110% at 80% 15%, rgba(196, 217, 233, 0.55), transparent 60%),
                        radial-gradient(90% 90% at 20% 25%, rgba(241, 196, 195, 0.6), transparent 70%),
                        radial-gradient(120% 80% at 50% 100%, rgba(187, 210, 203, 0.55), transparent 65%),
                        #f2ecdf;
                }
                body {
                    margin: 0;
                    min-height: 100vh;
                    display: flex;
                    flex-direction: column;
                    padding: 24px;
                    box-sizing: border-box;
                }
                h1, h2, h3 {
                    color: #3f4a61;
                }
                a {
                    color: inherit;
                    text-decoration: none;
                }
                .container {
                    width: min(1100px, 100%);
                    margin: 0 auto;
                }
                .hero {
                    text-align: center;
                    padding: 32px 24px 48px;
                    border-radius: 28px;
                    background: rgba(255, 255, 255, 0.85);
                    box-shadow: 0 25px 45px rgba(110, 128, 162, 0.18), inset 0 0 0 1px rgba(255, 255, 255, 0.35);
                }
                .hero-actions {
                    display: flex;
                    flex-direction: column;
                    gap: 12px;
                    align-items: center;
                    margin-top: 24px;
                }
                .cta {
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    border-radius: 999px;
                    padding: 14px 28px;
                    font-weight: 600;
                    background: linear-gradient(135deg, #9eb7d4, #f0c7cb);
                    color: #2d364c;
                    box-shadow: 0 15px 25px rgba(158, 183, 212, 0.25);
                }
                .secondary {
                    border: 1px solid rgba(128, 150, 176, 0.35);
                    background: rgba(237, 225, 226, 0.6);
                    color: #39445b;
                }
                .features {
                    display: grid;
                    gap: 20px;
                    margin-top: 40px;
                }
                .feature-card {
                    border-radius: 24px;
                    padding: 24px;
                    background: rgba(255, 255, 255, 0.92);
                    box-shadow: 0 20px 35px rgba(135, 155, 185, 0.18), inset 0 0 0 1px rgba(255, 255, 255, 0.3);
                    text-align: left;
                }
                @media (min-width: 768px) {
                    .hero { text-align: left; padding: 48px 56px; }
                    .hero-actions { flex-direction: row; justify-content: flex-start; }
                    .features { grid-template-columns: repeat(2, minmax(0, 1fr)); }
                }
            </style>
        @endif
    </head>
    <body class="font-sans antialiased text-muted">
        <div class="relative flex min-h-screen flex-col overflow-hidden">
            <header class="pt-10">
                <div class="mx-auto flex w-full max-w-6xl flex-col gap-16 px-4 sm:px-6 lg:px-8">
                    <div class="glass-panel flex items-center justify-between gap-4 rounded-full px-5 py-4 sm:px-7">
                        <a href="{{ url('/') }}" class="group flex items-center gap-3 text-lg font-semibold text-ink">
                            <span class="shimmer-border inline-flex h-10 w-10 items-center justify-center rounded-full bg-white/70 font-bold text-ink">R</span>
                            <span class="tracking-tight transition duration-200 group-hover:text-soft">Remixer</span>
                        </a>

                        @if (Route::has('login'))
                            <nav class="flex items-center gap-3 text-sm font-medium text-muted">
                                @auth
                                    <a href="{{ route('dashboard') }}" class="pill-button--ghost">
                                        Open dashboard
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="pill-button--ghost">
                                        Log in
                                    </a>

                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}" class="pill-button--primary">
                                            Get started
                                        </a>
                                    @endif
                                @endauth
                            </nav>
                        @endif
                    </div>

                    <div class="grid gap-12 lg:grid-cols-[minmax(0,1fr)_minmax(320px,1fr)] lg:items-center">
                        <div class="glass-panel rounded-3xl px-8 py-10 text-center lg:px-12 lg:py-14 lg:text-left">
                            <span class="badge-soft inline-flex items-center gap-2 self-center lg:self-start">
                                <span class="inline-flex h-2 w-2 rounded-full bg-ink"></span>
                                AI social remixing
                            </span>

                            <h1 class="mt-6 text-4xl font-semibold tracking-tight text-ink sm:text-5xl lg:text-6xl">
                                Turn every blog post into ten on-brand tweets in minutes.
                            </h1>

                            <p class="mt-6 text-lg leading-relaxed text-soft">
                                Remixer ingests your long-form content, generates polished tweets with Gemini Flash,
                                and keeps track of what’s pending, posted, or discarded so you never lose momentum.
                            </p>

                            <ul class="mt-8 flex flex-col gap-4 text-left text-sm text-muted lg:text-base">
                                <li class="flex items-start gap-3">
                                    <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white/70 text-sm font-semibold text-ink">1</span>
                                    Paste your article or import any saved post — Remixer stores drafts, archives, and search.
                                </li>
                                <li class="flex items-start gap-3">
                                    <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white/70 text-sm font-semibold text-ink">2</span>
                                    Generate ten tweet-ready ideas, edit them inline, and regenerate if you want a fresh take.
                                </li>
                                <li class="flex items-start gap-3">
                                    <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white/70 text-sm font-semibold text-ink">3</span>
                                    Mark tweets as posted or discarded, copy with one click, and jump straight to the X composer.
                                </li>
                            </ul>

                            <div class="mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row sm:justify-start">
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="pill-button--primary">
                                        Create your account
                                    </a>
                                @endif
                                <a href="#features" class="pill-button--ghost">
                                    Explore the features
                                </a>
                            </div>

                            <p class="mt-4 text-sm text-muted">Free registration • No credit card required • Built for indie writers</p>
                        </div>

                        <div class="glass-panel--tight rounded-3xl px-8 py-10 shadow-xl">
                            <div class="flex flex-col gap-6 text-left">
                                <div class="flex items-center justify-between">
                                    <span class="badge-soft">Sample Post</span>
                                    <span class="text-xs font-semibold uppercase tracking-wide text-soft">Live Workspace</span>
                                </div>
                                <div>
                                    <p class="text-xs uppercase tracking-wide text-soft">Blog title</p>
                                    <h2 class="mt-1 text-ink text-2xl font-semibold">Designing a kinder onboarding</h2>
                                    <p class="mt-3 text-sm leading-relaxed">
                                        “We distilled your 1,500 word article into a punchy social plan. Here’s what’s ready to go.”
                                    </p>
                                </div>

                                <div class="grid gap-3 sm:grid-cols-3">
                                    <div class="count-chip">
                                        <p class="text-xs uppercase tracking-wide text-soft">Pending</p>
                                        <p class="mt-1 text-2xl font-semibold text-ink">7</p>
                                    </div>
                                    <div class="count-chip">
                                        <p class="text-xs uppercase tracking-wide text-soft">Posted</p>
                                        <p class="mt-1 text-2xl font-semibold text-ink">12</p>
                                    </div>
                                    <div class="count-chip">
                                        <p class="text-xs uppercase tracking-wide text-soft">Discarded</p>
                                        <p class="mt-1 text-2xl font-semibold text-ink">3</p>
                                    </div>
                                </div>

                                <div class="surface-card p-6 text-sm leading-relaxed shadow-none">
                                    <div class="flex items-center justify-between">
                                        <span class="badge-soft">Tweet draft · 86 characters</span>
                                        <span class="text-xs text-soft">Last edited 2m ago</span>
                                    </div>
                                    <p class="mt-4 text-ink">
                                        “Users don’t churn because onboarding is short — they churn when onboarding hides the ‘why’.
                                        Here’s how we rewrote ours to teach, not test.”
                                    </p>
                                    <div class="mt-5 flex flex-wrap items-center gap-3 text-xs font-semibold text-soft">
                                        <span class="pill-button--ghost px-3 py-1">Mark posted</span>
                                        <span class="pill-button--ghost px-3 py-1">Copy</span>
                                        <span class="pill-button--ghost px-3 py-1">Open in X</span>
                                    </div>
                                </div>

                                <p class="text-xs text-soft">
                                    Remixer keeps every generated tweet editable, enforce the 280 character cap,
                                    and lets you regenerate a fresh batch whenever you need new angles.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex flex-col gap-20 pb-24">
                <section id="features" class="px-4 sm:px-6 lg:px-8">
                    <div class="mx-auto flex w-full max-w-5xl flex-col gap-12 text-center">
                        <div class="mx-auto max-w-3xl">
                            <span class="badge-soft">Why teams love Remixer</span>
                            <h2 class="mt-4 text-3xl font-semibold tracking-tight text-ink sm:text-4xl">
                                Ship a week’s worth of social content in a single deep work session.
                            </h2>
                            <p class="mt-4 text-lg leading-relaxed text-soft">
                                Built from the workflow of an indie blogger, Remixer removes the busywork from promoting your writing.
                                Every part of the app is designed to move you from “I just published” to “I’m ready to share everywhere”
                                without spreadsheets or copy-paste gymnastics.
                            </p>
                        </div>

                        <div class="grid gap-6 text-left md:grid-cols-2">
                            <article class="surface-card p-7">
                                <div class="flex items-center gap-3 text-sm font-semibold text-ink">
                                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/70">①</span>
                                    Capture your long-form content
                                </div>
                                <p class="mt-4 leading-relaxed text-muted">
                                    Paste any blog post, newsletter, or idea draft. Remixer stores the full article, lets you edit it later,
                                    archive completed pieces, and instantly search through everything you’ve ever uploaded.
                                </p>
                            </article>

                            <article class="surface-card p-7">
                                <div class="flex items-center gap-3 text-sm font-semibold text-ink">
                                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/70">②</span>
                                    Generate tweets with Gemini Flash
                                </div>
                                <p class="mt-4 leading-relaxed text-muted">
                                    Our Gemini Flash integration produces ten thoughtful, on-brand tweets per post,
                                    with your prompts tuned to avoid hashtags, mentions, or off-tone filler. Regenerate whenever you need new ideas.
                                </p>
                            </article>

                            <article class="surface-card p-7">
                                <div class="flex items-center gap-3 text-sm font-semibold text-ink">
                                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/70">③</span>
                                    Manage your tweet pipeline
                                </div>
                                <p class="mt-4 leading-relaxed text-muted">
                                    Track pending, posted, and discarded tweets at a glance. Mark tweets as “posted” to hide them from your queue,
                                    or discard the ones that don’t fit. You’ll never post the same thing twice by accident.
                                </p>
                            </article>

                            <article class="surface-card p-7">
                                <div class="flex items-center gap-3 text-sm font-semibold text-ink">
                                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/70">④</span>
                                    Publish faster than ever
                                </div>
                                <p class="mt-4 leading-relaxed text-muted">
                                    Edit tweets inline with instant validation, copy them with one click,
                                    or jump straight into the X composer. Remixer keeps a history so you can reuse what works and tweak the rest.
                                </p>
                            </article>
                        </div>
                    </div>
                </section>

                <section class="px-4 sm:px-6 lg:px-8">
                    <div class="mx-auto flex w-full max-w-6xl flex-col gap-12 rounded-3xl bg-white/70 px-8 py-14 text-center shadow-[0_25px_45px_rgba(110,128,162,0.18)] lg:px-16">
                        <div class="mx-auto max-w-3xl">
                            <span class="badge-soft">How Remixer fits in</span>
                            <h2 class="mt-4 text-3xl font-semibold tracking-tight text-ink sm:text-4xl">
                                A workflow designed for prolific creators
                            </h2>
                            <p class="mt-4 text-lg leading-relaxed text-soft">
                                Whether you publish weekly essays or ship product release notes, Remixer keeps your social promotion aligned with your voice.
                            </p>
                        </div>

                        <div class="grid gap-6 text-left sm:grid-cols-3">
                            <div class="surface-card p-6">
                                <h3 class="text-lg font-semibold text-ink">Stay organized</h3>
                                <p class="mt-3 text-sm leading-relaxed text-muted">
                                    Archive finished posts, restore them later, and always know how many unused tweets you have for every article.
                                </p>
                            </div>
                            <div class="surface-card p-6">
                                <h3 class="text-lg font-semibold text-ink">Collaborate smoothly</h3>
                                <p class="mt-3 text-sm leading-relaxed text-muted">
                                    Share an account, let teammates mark drafts as posted, and keep your social calendar synced without spreadsheets.
                                </p>
                            </div>
                            <div class="surface-card p-6">
                                <h3 class="text-lg font-semibold text-ink">Ship with confidence</h3>
                                <p class="mt-3 text-sm leading-relaxed text-muted">
                                    Built-in 280 character validation and copy previews ensure your tweets look great before you ever hit publish.
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="px-4 sm:px-6 lg:px-8">
                    <div class="mx-auto flex w-full max-w-4xl flex-col items-center gap-8 text-center">
                        <span class="badge-soft">Ready to remix your writing?</span>
                        <h2 class="text-3xl font-semibold tracking-tight text-ink sm:text-4xl">
                            Join Remixer and turn every article into a viral-ready tweet thread.
                        </h2>
                        <p class="max-w-2xl text-lg leading-relaxed text-soft">
                            Writers, founders, and content teams use Remixer to keep their social channels alive without sacrificing deep work time.
                            Sign up in seconds and have your first batch of tweets ready before your coffee cools.
                        </p>
                        <div class="flex flex-col items-center gap-4 sm:flex-row">
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="pill-button--primary">
                                    Register for Remixer
                                </a>
                            @endif
                            @if (Route::has('login'))
                                <a href="{{ route('login') }}" class="pill-button--ghost">
                                    Log in to existing account
                                </a>
                            @endif
                        </div>
                    </div>
                </section>
            </main>

            <footer class="pb-16 pt-10">
                <div class="mx-auto flex w-full max-w-6xl flex-col items-center gap-3 px-4 text-center text-sm text-soft sm:flex-row sm:justify-between sm:text-left">
                    <p>&copy; {{ date('Y') }} {{ config('app.name', 'Remixer') }}. Built for indie bloggers and creative teams.</p>
                    <p class="text-xs">
                        Need help? Reach out through your dashboard after registering. We love feedback.
                    </p>
                </div>
            </footer>
        </div>
    </body>
</html>

