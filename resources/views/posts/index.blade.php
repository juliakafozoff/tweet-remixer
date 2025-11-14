@extends('layouts.app')

@section('content')
    <section class="flex flex-col gap-8 text-center">
        <header class="glass-panel rounded-[30px] p-6 shadow-2xl shadow-black/20 md:flex md:items-center md:justify-between">
            <div>
                <div class="badge-soft">Dashboard</div>
                <h1 class="mt-3 text-3xl font-semibold">Your content cockpit</h1>
                <p class="mt-2 text-sm text-muted">
                    Track which drafts are ready to launch, which already flew, and remix more whenever you choose.
                </p>
            </div>
            <a href="{{ route('posts.create') }}" class="pill-button--primary gap-1 mt-6 w-full justify-center sm:mt-0 sm:w-auto">
                <svg style="width:0.9rem;height:0.9rem" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 5v14m7-7H5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                New post
            </a>
        </header>

        <form method="GET" action="{{ route('dashboard') }}" class="glass-panel rounded-[30px] p-6 shadow-2xl shadow-black/20">
            <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-end">
                <div>
                    <label for="search" class="text-xs font-semibold uppercase tracking-wide text-soft">Search posts</label>
                    <div class="mt-2 flex items-center gap-2 rounded-3xl border border-white/30 bg-white/40 px-4 py-3">
                        <svg style="width:0.9rem;height:0.9rem" class="text-soft" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 1 1 7.5-7.5 7.5 7.5 0 0 1-7.5 7.5z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <input id="search" name="q" type="search" value="{{ $search }}" placeholder="Search by title, callouts, or phrases"
                               class="input-field w-full bg-transparent placeholder:text-soft" />
                    </div>
                </div>
                <label class="glass-panel flex items-center justify-between gap-4 rounded-3xl border-none px-5 py-3 text-sm text-muted">
                    <span class="flex items-center gap-2">
                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-white/70 text-xs font-semibold uppercase tracking-wide text-ink">
                            A
                        </span>
                        Include archived posts
                    </span>
                    <input type="hidden" name="include_archived" value="0" />
                    <input type="checkbox" name="include_archived" value="1" {{ $includeArchived ? 'checked' : '' }}
                           class="h-5 w-9 rounded-full border border-white/40 bg-white/60 text-soft focus:ring-0" />
                </label>
            </div>
        </form>

        @if ($posts->isEmpty())
            <div class="glass-panel rounded-[30px] border-dashed border-white/35 bg-white/40 p-12 text-center shadow-2xl shadow-black/20">
                <p class="text-lg font-semibold text-ink">No posts yet</p>
                <p class="mt-3 text-sm text-muted">Save your first article and we’ll carve it into ten punchy tweets in seconds.</p>
                <a href="{{ route('posts.create') }}" class="pill-button--primary mt-6 inline-flex">
                    Add your first post
                </a>
            </div>
        @else
            <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                @foreach ($posts as $post)
                    <article class="surface-card flex flex-col justify-between rounded-[28px] p-6">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between gap-2 text-xs uppercase tracking-wide text-soft">
                                <span class="badge-soft {{ $post->archived_at ? 'border-rose-300/40' : 'border-emerald-300/40' }}">
                                    {{ $post->archived_at ? 'Archived' : 'Active' }}
                                </span>
                                <span class="text-muted">{{ $post->created_at->format('M j, Y') }}</span>
                            </div>

                            <h2 class="text-xl font-semibold text-ink line-clamp-2">{{ $post->title }}</h2>

                            <p class="text-sm leading-relaxed text-muted line-clamp-3">{{ \Illuminate\Support\Str::limit(strip_tags($post->body), 180) }}</p>
                        </div>

                        <dl class="mt-6 grid grid-cols-3 gap-3 p-4 text-center text-sm text-muted">
                            <div class="count-chip">
                                <dt class="text-xs uppercase tracking-wide text-soft">Pending</dt>
                <dd class="mt-1 text-lg font-semibold text-ink">{{ $post->pending_tweets_count }}</dd>
                            </div>
                            <div class="count-chip">
                                <dt class="text-xs uppercase tracking-wide text-soft">Posted</dt>
                                <dd class="mt-1 text-lg font-semibold text-ink">{{ $post->posted_tweets_count }}</dd>
                            </div>
                            <div class="count-chip">
                                <dt class="text-xs uppercase tracking-wide text-soft">Discarded</dt>
                                <dd class="mt-1 text-lg font-semibold text-ink">{{ $post->discarded_tweets_count }}</dd>
                            </div>
                        </dl>

                        <div class="mt-6 flex items-center justify-between gap-2">
                            <a href="{{ route('posts.show', $post) }}" class="pill-button--primary">
                                Manage tweets
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M8 5l8 7-8 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>

                            <form method="POST" action="{{ $post->archived_at ? route('posts.restore', $post) : route('posts.archive', $post) }}">
                                @csrf
                                <button type="submit" class="pill-button--outline">
                                    {{ $post->archived_at ? 'Restore' : 'Archive' }}
                                </button>
                            </form>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-8 glass-panel rounded-3xl p-4 shadow-2xl shadow-black/20">
                {{ $posts->links() }}
            </div>
        @endif
    </section>
@endsection

