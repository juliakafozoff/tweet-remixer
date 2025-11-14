@extends('layouts.app')

@section('content')
    <article class="space-y-10 text-center">
        <header class="glass-panel rounded-[32px] p-8 shadow-2xl shadow-black/20">
            <div class="flex flex-col gap-6 md:flex-row md:items-start md:justify-between">
                <div class="space-y-4 text-left md:text-left">
                    <div class="badge-soft {{ $post->archived_at ? 'border-rose-300/40' : 'border-emerald-300/40' }}">
                        {{ $post->archived_at ? 'Archived' : 'Active' }} · Created {{ $post->created_at->diffForHumans() }}
                    </div>

                    <h1 class="text-3xl font-semibold text-ink">{{ $post->title }}</h1>

                    @if ($post->source_url)
                        <a href="{{ $post->source_url }}" target="_blank" rel="noopener" class="pill-button--ghost w-fit">
                            Visit source
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M14 3h7v7m0-7-9 9m-4 2H3v7h7v-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </a>
                    @endif
                </div>

                <div class="flex flex-wrap items-center justify-center gap-3 md:justify-end">
                    <a href="{{ route('posts.edit', $post) }}" class="pill-button--ghost">
                        Edit post
                    </a>

                    <form method="POST" action="{{ $post->archived_at ? route('posts.restore', $post) : route('posts.archive', $post) }}">
                        @csrf
                        <button type="submit" class="pill-button--outline {{ $post->archived_at ? 'border-emerald-400/50 text-emerald-200 hover:border-emerald-300 hover:text-emerald-100' : '' }}">
                            {{ $post->archived_at ? 'Restore' : 'Archive' }}
                        </button>
                    </form>

                    <form method="POST" action="{{ route('posts.destroy', $post) }}" onsubmit="return confirm('Delete this post and all related tweets?')" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="pill-button--outline border-rose-400/60 text-rose-200 hover:border-rose-300 hover:text-rose-100">
                            Delete
                        </button>
                    </form>
                </div>
            </div>

            <dl class="mt-8 grid gap-5 rounded-3xl border border-white/20 bg-white/40 p-6 sm:grid-cols-3">
                <div class="count-chip">
                    <dt class="text-xs uppercase tracking-wide text-soft">Pending drafts</dt>
                    <dd class="mt-2 text-2xl font-semibold text-ink">{{ $post->tweets->where('status', 'pending')->count() }}</dd>
                </div>
                <div class="count-chip">
                    <dt class="text-xs uppercase tracking-wide text-soft">Posted</dt>
                    <dd class="mt-2 text-2xl font-semibold text-ink">{{ $post->tweets->where('status', 'posted')->count() }}</dd>
                </div>
                <div class="count-chip">
                    <dt class="text-xs uppercase tracking-wide text-soft">Discarded</dt>
                    <dd class="mt-2 text-2xl font-semibold text-ink">{{ $post->tweets->where('status', 'discarded')->count() }}</dd>
                </div>
            </dl>

            <details class="group mt-8 rounded-3xl border border-white/25 bg-white/40 p-4">
                <summary class="cursor-pointer text-sm font-semibold text-soft transition group-open:text-ink">Show full blog content</summary>
                <div class="gradient-divider mt-4"></div>
                <div class="mt-4 space-y-4 rounded-2xl border border-white/30 bg-white/60 p-5 text-sm leading-6 text-muted text-left">
                    {!! nl2br(e($post->body)) !!}
                </div>
            </details>
        </header>

        <section class="glass-panel rounded-[32px] p-8 shadow-2xl shadow-black/20 text-left">
            <header class="flex flex-col gap-4 border-b border-white/20 pb-6 md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="text-2xl font-semibold text-ink">Tweet drafts</h2>
                    <p class="mt-1 text-sm text-muted">Generate fresh ideas or iterate on existing ones before posting.</p>
                </div>
                <div class="flex flex-wrap items-center gap-4">
                    <form method="POST" action="{{ route('posts.tweets.generate', $post) }}" class="glass-panel flex items-center gap-3 rounded-full border-none px-4 py-2 text-sm">
                        @csrf
                        <label for="generate-count" class="text-soft">Generate</label>
                        <input id="generate-count" name="count" type="number" min="1" max="20" value="10"
                               class="input-field w-16 rounded-full px-3 py-1 text-center" />
                        <button type="submit" class="pill-button--primary">
                            Go
                        </button>
                    </form>
                    <form method="POST" action="{{ route('posts.tweets.regenerate', $post) }}" class="glass-panel flex items-center gap-3 rounded-full border-none bg-white/40 px-4 py-2 text-sm">
                        @csrf
                        <label for="regenerate-count" class="text-soft">Regenerate</label>
                        <input id="regenerate-count" name="count" type="number" min="1" max="20" value="10"
                               class="input-field w-16 rounded-full px-3 py-1 text-center" />
                        <button type="submit" class="pill-button inline-flex items-center justify-center rounded-full bg-emerald-400 px-3 py-1 text-slate-950 shadow-lg shadow-emerald-400/30 hover:brightness-110">
                            Refresh
                        </button>
                    </form>
                </div>
            </header>

            @if ($post->tweets->isEmpty())
                <div class="mt-6 rounded-3xl border border-dashed border-white/35 bg-white/40 p-10 text-center">
                    <p class="text-lg font-medium text-ink">No tweets yet.</p>
                    <p class="mt-2 text-sm text-muted">Generate a batch to kickstart your social posts.</p>
                </div>
            @else
                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    @foreach ($post->tweets as $tweet)
                        <div class="surface-card flex flex-col justify-between rounded-3xl p-5">
                            <div class="space-y-3">
                                <div class="flex items-center justify-between text-xs uppercase tracking-wide text-soft">
                                    <span class="rounded-full px-2 py-1 font-semibold
                                        @class([
                                            'bg-[rgba(158,183,212,0.3)] text-ink' => $tweet->status === 'pending',
                                            'bg-[rgba(168,198,177,0.3)] text-ink' => $tweet->status === 'posted',
                                            'bg-[rgba(240,199,203,0.3)] text-ink' => $tweet->status === 'discarded',
                                        ])
                                    ">
                                        {{ ucfirst($tweet->status) }}
                                    </span>
                                    <span class="text-soft">{{ $tweet->updated_at->diffForHumans() }}</span>
                                </div>

                                <p class="tweet-content whitespace-pre-wrap text-sm leading-6 text-ink" data-tweet-id="{{ $tweet->id }}">
                                    {{ $tweet->content }}
                                </p>

                                @if ($tweet->status === 'pending')
                                    <form method="POST" action="{{ route('tweets.update', $tweet) }}" class="tweet-edit-form hidden space-y-3" data-tweet-id="{{ $tweet->id }}">
                                        @csrf
                                        @method('PATCH')
                                        <textarea name="content" rows="4" maxlength="280"
                                                  class="textarea-field w-full">{{ $tweet->content }}</textarea>
                                        <div class="flex items-center gap-2">
                                            <button type="submit" class="pill-button--primary px-4 py-1.5">Save</button>
                                            <button type="button" class="tweet-edit-cancel pill-button--ghost px-4 py-1.5" data-tweet-id="{{ $tweet->id }}">Cancel</button>
                                        </div>
                                    </form>
                                @endif
                            </div>

                            <div class="mt-5 flex flex-wrap items-center gap-2 text-sm">
                                <button type="button" class="tweet-copy pill-button--ghost" data-content="{{ base64_encode($tweet->content) }}" data-content-encoding="base64">
                                    Copy
                                </button>
                                <a href="https://x.com/intent/tweet?text={{ urlencode($tweet->content) }}" target="_blank" rel="noopener"
                                   class="pill-button--outline border-white/40 hover:bg-white/70 hover:text-ink">
                                    Tweet it
                                </a>

                                @if ($tweet->status === 'pending')
                                    <button type="button" class="tweet-edit-toggle pill-button--outline border-white/40 hover:bg-white/70 hover:text-ink" data-tweet-id="{{ $tweet->id }}">
                                        Edit
                                    </button>

                                    <form method="POST" action="{{ route('tweets.mark-posted', $tweet) }}" onsubmit="return confirm('Mark this tweet as posted?')" class="inline">
                                        @csrf
                                        <button type="submit" class="pill-button inline-flex items-center gap-2 rounded-full border border-white/30 bg-white/40 px-3 py-1.5 text-ink transition hover:bg-white hover:text-ink">
                                            Mark posted
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('tweets.discard', $tweet) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="pill-button inline-flex items-center gap-2 rounded-full border border-white/30 bg-white/40 px-3 py-1.5 text-ink transition hover:bg-white hover:text-ink">
                                            Discard
                                        </button>
                                    </form>
                                @endif

                                @if ($tweet->status === 'posted')
                                    <form method="POST" action="{{ route('tweets.restore', $tweet) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="pill-button--outline">
                                            Move to drafts
                                        </button>
                                    </form>
                                @endif

                                @if ($tweet->status === 'discarded')
                                    <form method="POST" action="{{ route('tweets.restore', $tweet) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="pill-button--outline">
                                            Restore
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    </article>
@endsection

