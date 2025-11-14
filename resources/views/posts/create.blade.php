@extends('layouts.app')

@section('content')
    <section class="mx-auto max-w-4xl space-y-8 text-center">
        <header class="glass-panel rounded-[30px] p-6 shadow-2xl shadow-black/20">
            <div class="badge-soft">New post</div>
            <h1 class="mt-3 text-3xl font-semibold text-ink">Drop in your source material</h1>
            <p class="mt-2 text-sm text-muted">Store the full article or blog post. We’ll remix it into tweet-ready highlights whenever you need fresh social content.</p>
        </header>

        <form method="POST" action="{{ route('posts.store') }}" class="section-shell space-y-6 text-left">
            @csrf

            <div class="space-y-2">
                <label for="title" class="text-xs font-semibold uppercase tracking-wide text-soft">Post title</label>
                <input id="title" name="title" type="text" value="{{ old('title') }}" required maxlength="200"
                       class="input-field w-full" placeholder="E.g. 7 lessons from shipping side projects" />
            </div>

            <div class="space-y-2">
                <label for="body" class="text-xs font-semibold uppercase tracking-wide text-soft">Blog content</label>
                <textarea id="body" name="body" rows="16" required
                          class="textarea-field w-full" placeholder="Paste the full article, including headings and callouts">{{ old('body') }}</textarea>
                <p class="text-xs text-soft">Tip: include any unique phrases or key takeaways—Remixer keeps them intact when writing tweets.</p>
            </div>

            <div class="space-y-2">
                <label for="source_url" class="text-xs font-semibold uppercase tracking-wide text-soft">Source link (optional)</label>
                <input id="source_url" name="source_url" type="url" value="{{ old('source_url') }}" maxlength="500"
                       class="input-field w-full" placeholder="https://yourblog.com/post" />
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('dashboard') }}" class="pill-button--ghost">
                    Cancel
                </a>
                <button type="submit" class="pill-button--primary">
                    Save post
                </button>
            </div>
        </form>
    </section>
@endsection

