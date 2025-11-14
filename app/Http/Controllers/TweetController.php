<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Tweet;
use App\Services\TweetGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class TweetController extends Controller
{
    public function generate(Request $request, Post $post, TweetGenerator $generator): RedirectResponse
    {
        $this->authorizePost($post);

        $count = $request->integer('count', 10);
        $count = max(1, min(20, $count));

        try {
            $tweets = $generator->generateFromPost($post, $count);
        } catch (RuntimeException $exception) {
            return redirect()->route('posts.show', $post)->withErrors([
                'generation' => $exception->getMessage(),
            ]);
        }

        $generated = count($tweets);

        DB::transaction(function () use ($post, $tweets) {
            foreach ($tweets as $tweet) {
                $post->tweets()->create([
                    'content' => $tweet,
                    'status' => 'pending',
                    'posted_at' => null,
                    'discarded_at' => null,
                ]);
            }
        });

        return redirect()->route('posts.show', $post)->with('status', "{$generated} tweets generated and ready for review.");
    }

    public function regenerate(Request $request, Post $post, TweetGenerator $generator): RedirectResponse
    {
        $this->authorizePost($post);

        $count = $request->integer('count', 10);
        $count = max(1, min(20, $count));

        try {
            $tweets = $generator->generateFromPost($post, $count);
        } catch (RuntimeException $exception) {
            return redirect()->route('posts.show', $post)->withErrors([
                'generation' => $exception->getMessage(),
            ]);
        }

        $generated = count($tweets);

        DB::transaction(function () use ($post, $tweets) {
            $post->tweets()->whereIn('status', ['pending', 'discarded'])->delete();

            foreach ($tweets as $tweet) {
                $post->tweets()->create([
                    'content' => $tweet,
                    'status' => 'pending',
                ]);
            }
        });

        return redirect()->route('posts.show', $post)->with('status', "Replaced drafts with {$generated} fresh tweets.");
    }

    public function update(Request $request, Tweet $tweet): RedirectResponse
    {
        $this->authorizeTweet($tweet);

        if ($tweet->status !== 'pending') {
            throw ValidationException::withMessages([
                'content' => 'Only pending tweets can be edited.',
            ]);
        }

        $data = $request->validate([
            'content' => ['required', 'string', 'max:280'],
        ]);

        $tweet->update([
            'content' => trim($data['content']),
        ]);

        return back()->with('status', 'Tweet updated.');
    }

    public function markPosted(Tweet $tweet): RedirectResponse
    {
        $this->authorizeTweet($tweet);

        if ($tweet->status === 'posted') {
            return back()->with('status', 'Tweet already marked as posted.');
        }

        $tweet->update([
            'status' => 'posted',
            'posted_at' => now(),
            'discarded_at' => null,
        ]);

        return back()->with('status', 'Tweet marked as posted.');
    }

    public function discard(Tweet $tweet): RedirectResponse
    {
        $this->authorizeTweet($tweet);

        if ($tweet->status === 'discarded') {
            return back()->with('status', 'Tweet already discarded.');
        }

        $tweet->update([
            'status' => 'discarded',
            'discarded_at' => now(),
            'posted_at' => null,
        ]);

        return back()->with('status', 'Tweet discarded.');
    }

    public function restore(Tweet $tweet): RedirectResponse
    {
        $this->authorizeTweet($tweet);

        if ($tweet->status === 'pending') {
            return back()->with('status', 'Tweet already ready to use.');
        }

        $tweet->update([
            'status' => 'pending',
            'discarded_at' => null,
            'posted_at' => null,
        ]);

        return back()->with('status', 'Tweet restored to drafts.');
    }

    protected function authorizePost(Post $post): void
    {
        abort_if($post->user_id !== Auth::id(), 403);
    }

    protected function authorizeTweet(Tweet $tweet): void
    {
        $this->authorizePost($tweet->post);
    }
}

