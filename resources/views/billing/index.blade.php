@extends('layouts.app')

@section('content')
    <div class="glass-panel rounded-3xl px-6 py-6">
        <div class="flex flex-col gap-4">
            <div>
                <p class="text-sm uppercase tracking-wide text-muted">Membership</p>
                <h1 class="mt-1 text-2xl font-semibold text-ink">Keep Remixer running</h1>
                <p class="mt-2 text-soft">Access to Remixer costs <span class="font-semibold text-ink">$10/month</span> after a {{ $trialDays }}-day free trial.</p>
            </div>

            <div class="rounded-2xl border border-white/30 bg-white/60 p-4 text-sm text-soft">
                <p class="font-medium text-ink">Status:</p>
                @if ($hasAccess)
                    <p class="mt-1 text-emerald-600">
                        {{ $subscription && $subscription->onGracePeriod() ? 'Ends soon' : 'Active' }}
                    </p>
                    @if ($user->onTrial('default'))
                        <p class="mt-1">Your free trial ends {{ $user->trialEndsAt('default')?->timezone(config('app.timezone'))->toFormattedDateString() }}.</p>
                    @elseif ($subscription && $subscription->onGracePeriod())
                        <p class="mt-1">You can keep using Remixer until {{ $subscription->ends_at?->timezone(config('app.timezone'))->toFormattedDateString() }}.</p>
                    @else
                        <p class="mt-1">Thanks for being a subscriber!</p>
                    @endif
                @else
                    <p class="mt-1 text-rose-600">Inactive — start your trial to continue using Remixer.</p>
                @endif
            </div>
        </div>
    </div>

    @if (! $hasAccess)
        <div class="glass-panel mt-6 rounded-3xl px-6 py-6">
            <h2 class="text-xl font-semibold text-ink">Start your free trial</h2>
            <p class="mt-2 text-soft">We’ll send you to Stripe’s secure checkout. You won’t be charged until the {{ $trialDays }}-day trial ends.</p>

            @if (! $priceId)
                <div class="mt-4 rounded-2xl border border-rose-200 bg-rose-50/70 p-4 text-sm text-rose-700">
                    Stripe is not configured yet. Add STRIPE_PRICE_ID to your environment, then reload this page.
                </div>
            @else
                <form class="mt-6 space-y-4" action="{{ route('billing.subscribe') }}" method="POST">
                    @csrf

                    <button type="submit" class="pill-button--primary w-full justify-center">
                        Continue on Stripe
                    </button>

                    <p class="text-center text-xs text-muted">Stripe opens in a new tab. Once you complete checkout you’ll be sent back here.</p>
                </form>
            @endif
        </div>
    @endif

    @if ($subscription)
        <div class="glass-panel mt-6 rounded-3xl px-6 py-6">
            <h2 class="text-xl font-semibold text-ink">Manage subscription</h2>
            <div class="mt-4 flex flex-col gap-3 text-sm text-soft">
                <p>Plan: $10/month</p>
                @if ($subscription->cancelled())
                    <p>Ends: {{ $subscription->ends_at?->timezone(config('app.timezone'))->toDayDateTimeString() }}</p>
                @else
                    <p>Renews: {{ $subscription->asStripeSubscription()->current_period_end ? \Illuminate\Support\Carbon::createFromTimestamp($subscription->asStripeSubscription()->current_period_end)->toDayDateTimeString() : 'Monthly' }}</p>
                @endif
            </div>

            <div class="mt-6 flex flex-wrap gap-3">
                @if ($subscription->active() && ! $subscription->onGracePeriod())
                    <form method="POST" action="{{ route('billing.cancel') }}">
                        @csrf
                        <button type="submit" class="pill-button--outline">Cancel at period end</button>
                    </form>
                @elseif ($subscription->onGracePeriod())
                    <form method="POST" action="{{ route('billing.resume') }}">
                        @csrf
                        <button type="submit" class="pill-button--primary">Resume subscription</button>
                    </form>
                @endif

                <form method="POST" action="{{ route('billing.portal') }}">
                    @csrf
                    <button type="submit" class="pill-button--ghost">Open Stripe customer portal</button>
                </form>
            </div>
        </div>
    @endif
@endsection

