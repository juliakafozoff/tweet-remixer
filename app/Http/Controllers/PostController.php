<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(Request $request): View
    {
        $query = Post::query()
            ->where('user_id', $request->user()->id)
            ->when($request->boolean('include_archived') === false, fn ($query) => $query->whereNull('archived_at'))
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%' . trim($request->input('q')) . '%';

                $query->where(function ($query) use ($term) {
                    $query->where('title', 'like', $term)
                        ->orWhere('body', 'like', $term);
                });
            })
            ->withCount([
                'tweets as pending_tweets_count' => fn ($query) => $query->where('status', 'pending'),
                'tweets as posted_tweets_count' => fn ($query) => $query->where('status', 'posted'),
                'tweets as discarded_tweets_count' => fn ($query) => $query->where('status', 'discarded'),
            ])
            ->latest();

        $posts = $query->paginate(10)->withQueryString();

        return view('posts.index', [
            'posts' => $posts,
            'search' => $request->input('q', ''),
            'includeArchived' => $request->boolean('include_archived'),
        ]);
    }

    public function create(): View
    {
        return view('posts.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'body' => ['required', 'string'],
            'source_url' => ['nullable', 'url', 'max:500'],
        ]);

        $post = $request->user()->posts()->create($data);

        return redirect()->route('posts.show', $post)->with('status', 'Post saved. Generate tweets when you are ready!');
    }

    public function show(Post $post): View
    {
        $this->authorizePost($post);

        $post->load([
            'tweets' => fn ($query) => $query
                ->orderByRaw("CASE status WHEN 'pending' THEN 0 WHEN 'posted' THEN 1 ELSE 2 END")
                ->orderByDesc('created_at'),
        ]);

        return view('posts.show', compact('post'));
    }

    public function edit(Post $post): View
    {
        $this->authorizePost($post);

        return view('posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post): RedirectResponse
    {
        $this->authorizePost($post);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'body' => ['required', 'string'],
            'source_url' => ['nullable', 'url', 'max:500'],
        ]);

        $post->update($data);

        return redirect()->route('posts.show', $post)->with('status', 'Post updated.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $this->authorizePost($post);

        $post->delete();

        return redirect()->route('dashboard')->with('status', 'Post deleted.');
    }

    public function archive(Post $post): RedirectResponse
    {
        $this->authorizePost($post);

        $post->update(['archived_at' => now()]);

        return redirect()->route('dashboard')->with('status', 'Post archived.');
    }

    public function restore(Post $post): RedirectResponse
    {
        $this->authorizePost($post);

        $post->update(['archived_at' => null]);

        return redirect()->route('dashboard')->with('status', 'Post restored.');
    }

    protected function authorizePost(Post $post): void
    {
        abort_if($post->user_id !== Auth::id(), 403);
    }
}

