<?php

namespace App\Http\Controllers;

use Stripe\Stripe;
use Stripe\Account;
use Stripe\AccountLink;
use Illuminate\Support\Facades\Auth;

class StripeConnectController extends Controller
{
    public function onboard()
    {
        if (!Auth::user()->isWorker()) {
            return back()->withErrors(['connect' => 'Only workers can set up payouts.']);
        }

        if (!config('stripe.sk')) {
            return back()->withErrors(['connect' => 'Stripe is not configured.']);
        }

        if (!config('stripe.connect_enabled')) {
            return back()->withErrors([
                'connect' => 'Stripe Connect is disabled in this environment. Client payments still work; worker payouts are recorded as pending until Connect is enabled in production.',
            ]);
        }

        Stripe::setApiKey(config('stripe.sk'));
        $user = Auth::user();

        try {
            if (!$user->stripe_connect_id) {
                $account = Account::create([
                    'type' => 'express',
                    'country' => 'US',
                    'email' => $user->email,
                    'capabilities' => [
                        'transfers' => ['requested' => true],
                    ],
                ]);

                $user->update(['stripe_connect_id' => $account->id]);
            }

            $link = AccountLink::create([
                'account' => $user->stripe_connect_id,
                'refresh_url' => route('connect.refresh'),
                'return_url' => route('profile'),
                'type' => 'account_onboarding',
            ]);

            return redirect()->away($link->url);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            return back()->withErrors([
                'connect' => 'Stripe Connect is not enabled on your Stripe account. Enable it at https://dashboard.stripe.com/settings/connect (use Test mode while developing), then try again.',
            ]);
        }
    }

    public function refresh()
    {
        return redirect()->route('connect.onboard');
    }
}
