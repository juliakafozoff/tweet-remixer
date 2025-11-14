@extends('layouts.app')

@section('content')
    <section class="mx-auto max-w-4xl space-y-8 text-center">
        <header class="glass-panel rounded-[30px] p-6 shadow-2xl shadow-black/20">
            <div class="badge-soft">Edit post</div>
            <h1 class="mt-3 text-3xl font-semibold text-ink">Refine your source before remixing again</h1>
            <p class="mt-2 text-sm text-muted">Tweak the headline, sharpen the body copy, or add new context before generating a fresh run of tweets.</p>
        </header>

        <form method="POST" action="{{ route('posts.update', $post) }}" class="section-shell space-y-6 text-left">
            @csrf
            @method('PUT')

            <div class="space-y-2">
                <label for="title" class="text-xs font-semibold uppercase tracking-wide text-soft">Post title</label>
                <input id="title" name="title" type="text" value="{{ old('title', $post->title) }}" required maxlength="200"
                       class="input-field w-full" />
            </div>

            <div class="space-y-2">
                <label for="body" class="text-xs font-semibold uppercase tracking-wide text-soft">Blog content</label>
                <textarea id="body" name="body" rows="16" required
                          class="textarea-field w-full">{{ old('body', $post->body) }}</textarea>
            </div>

            <div class="space-y-2">
                <label for="source_url" class="text-xs font-semibold uppercase tracking-wide text-soft">Source link (optional)</label>
                <input id="source_url" name="source_url" type="url" value="{{ old('source_url', $post->source_url) }}" maxlength="500"
                       class="input-field w-full" />
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('posts.show', $post) }}" class="pill-button--ghost">
                    Cancel
                </a>
                <button type="submit" class="pill-button--primary">
                    Save changes
                </button>
            </div>
        </form>

        <form method="POST" action="{{ route('posts.destroy', $post) }}" onsubmit="return confirm('Delete this post and all related tweets?')" class="flex justify-end">
            @csrf
            @method('DELETE')
            <button type="submit" class="pill-button--outline border-rose-400/60 text-rose-200 hover:border-rose-300 hover:text-rose-100">
                Delete post
            </button>
        </form>
    </section>
@endsection

