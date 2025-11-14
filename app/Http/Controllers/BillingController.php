<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BillingController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $hasActiveSub = $user->subscribed('default') || $user->onTrial('default');

        return view('billing.index', [
            'user' => $user,
            'priceId' => config('services.stripe.price_id'),
            'trialDays' => (int) config('services.stripe.trial_days', 7),
            'subscription' => $user->subscription('default'),
            'hasAccess' => $hasActiveSub,
        ]);
    }

    public function subscribe(Request $request): RedirectResponse
    {
        $user = $request->user();
        $priceId = config('services.stripe.price_id');

        if (! $priceId) {
            abort(500, 'Stripe price ID is not configured.');
        }

        if ($user->subscribed('default')) {
            return redirect()->route('billing.show')->with('status', 'Your subscription is already active.');
        }

        return $user->newSubscription('default', $priceId)
            ->trialDays((int) config('services.stripe.trial_days', 7))
            ->checkout([
                'success_url' => route('billing.success').'?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('billing.show'),
            ]);
    }

    public function cancel(Request $request): RedirectResponse
    {
        $subscription = $request->user()->subscription('default');

        if (! $subscription) {
            return redirect()->route('billing.show')->with('status', 'No active subscription to cancel.');
        }

        if ($subscription->cancelled()) {
            return redirect()->route('billing.show')->with('status', 'Your subscription is already canceled.');
        }

        $subscription->cancel();

        return redirect()->route('billing.show')->with('status', 'Subscription canceled. You can keep using Remixer until the period ends.');
    }

    public function resume(Request $request): RedirectResponse
    {
        $subscription = $request->user()->subscription('default');

        if (! $subscription || ! $subscription->onGracePeriod()) {
            return redirect()->route('billing.show')->with('status', 'Nothing to resume.');
        }

        $subscription->resume();

        return redirect()->route('billing.show')->with('status', 'Subscription resumed.');
    }

    public function portal(Request $request): RedirectResponse
    {
        return redirect()->away(
            $request->user()->billingPortalUrl(route('billing.show'))
        );
    }

    public function success(Request $request): RedirectResponse
    {
        $user = $request->user()->fresh();

        if ($user->subscribed('default')) {
            return redirect()->route('dashboard')->with('status', 'Thanks! Your subscription is active.');
        }

        return redirect()->route('billing.show')->with(
            'status',
            'Thanks! Stripe is finishing your subscription. You’ll get access as soon as it completes.'
        );
    }
}
