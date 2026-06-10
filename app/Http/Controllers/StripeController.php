<?php

namespace App\Http\Controllers;

use App\Models\Tasks;
use App\Models\Payment;
use App\Models\Payout;
use App\Models\Milestone;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\Transfer;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;
use Stripe\Exception\SignatureVerificationException;

class StripeController extends Controller
{
    public function checkout()
    {
        return back()->with('message', 'Payment was cancelled.');
    }

    public function session(Request $request)
    {
        $request->validate([
            'task' => 'required|exists:tasks,id',
            'milestone' => 'nullable|exists:milestones,id',
        ]);

        $task = Tasks::with(['gig', 'milestones'])->findOrFail($request->task);
        $milestone = null;
        $amount = null;
        $description = '';

        if ((int) $task->owner !== Auth::id()) {
            abort(403, 'Unauthorized action');
        }

        if ($request->milestone) {
            $milestone = Milestone::where('task_id', $task->id)->findOrFail($request->milestone);

            if (!$milestone->canPay()) {
                return back()->withErrors(['payment' => 'This milestone is not ready for payment.']);
            }

            $amount = $milestone->amount;
            $description = 'Milestone: ' . $milestone->title;
        } else {
            if (!$task->canPay()) {
                return back()->withErrors(['payment' => 'Payment is only available after deliverables are approved.']);
            }

            $amount = $task->price ?? $task->gig?->salary;
            $description = $task->gig?->title ?? 'DevLancer Contract #' . $task->id;
        }

        if (!$amount || $amount <= 0) {
            return back()->withErrors(['payment' => 'Invalid payment amount.']);
        }

        Stripe::setApiKey(config('stripe.sk'));

        $payment = Payment::create([
            'task_id' => $task->id,
            'milestone_id' => $milestone?->id,
            'user_id' => Auth::id(),
            'amount' => $amount,
            'currency' => 'USD',
            'status' => Payment::STATUS_PENDING,
        ]);

        $session = Session::create([
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'USD',
                        'product_data' => [
                            'name' => $description,
                            'description' => 'Payment by ' . Auth::user()->name . ' for contract #' . $task->id,
                        ],
                        'unit_amount' => (int) round($amount * 100),
                    ],
                    'quantity' => 1,
                ],
            ],
            'mode' => 'payment',
            'success_url' => route('success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('checkout'),
            'metadata' => [
                'task_id' => $task->id,
                'payment_id' => $payment->id,
                'milestone_id' => $milestone?->id,
            ],
        ]);

        $payment->update(['stripe_session_id' => $session->id]);

        return redirect()->away($session->url);
    }

    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');

        if (!$sessionId) {
            return redirect()->route('profile')->withErrors(['payment' => 'Invalid payment session.']);
        }

        try {
            Stripe::setApiKey(config('stripe.sk'));
            $session = Session::retrieve($sessionId);

            if ($session->payment_status === 'paid') {
                $this->markPaymentCompleted($session->id);
            }
        } catch (\Exception $e) {
            return redirect()->route('profile')->withErrors(['payment' => 'Could not verify payment.']);
        }

        return redirect()->route('profile')->with('message', 'Payment completed successfully.');
    }

    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('stripe.webhook_secret');

        if (!$webhookSecret) {
            return response('Webhook secret not configured', 500);
        }

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (SignatureVerificationException $e) {
            return response('Invalid signature', 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;

            if ($session->payment_status === 'paid') {
                $this->markPaymentCompleted($session->id);
            }
        }

        return response('OK', 200);
    }

    private function markPaymentCompleted(string $stripeSessionId): void
    {
        $payment = Payment::with(['task.user', 'milestone'])->where('stripe_session_id', $stripeSessionId)->first();

        if (!$payment || $payment->status === Payment::STATUS_COMPLETED) {
            return;
        }

        $payment->update([
            'status' => Payment::STATUS_COMPLETED,
            'paid_at' => now(),
        ]);

        $task = $payment->task;

        if ($payment->milestone_id) {
            $payment->milestone->update(['status' => Milestone::STATUS_PAID]);
            $task->markFullyPaidIfReady();
        } else {
            $task->update(['payment_flag' => 1]);
        }

        $this->createPayout($payment);

        NotificationService::send(
            $task->user,
            'Payment received',
            'Payment of $' . $payment->amount . ' received for contract #' . $task->id,
            '/tasks/' . $task->id,
            'payment'
        );
    }

    private function createPayout(Payment $payment): void
    {
        $task = $payment->task;
        $worker = $task->user;

        $payout = Payout::create([
            'payment_id' => $payment->id,
            'worker_id' => $worker->id,
            'amount' => $payment->amount,
            'status' => Payout::STATUS_PENDING,
        ]);

        if ($worker->stripe_connect_id && config('stripe.sk')) {
            try {
                Stripe::setApiKey(config('stripe.sk'));
                $transfer = Transfer::create([
                    'amount' => (int) round($payment->amount * 100),
                    'currency' => 'usd',
                    'destination' => $worker->stripe_connect_id,
                    'description' => 'DevLancer payout for contract #' . $task->id,
                ]);

                $payout->update([
                    'status' => Payout::STATUS_TRANSFERRED,
                    'stripe_transfer_id' => $transfer->id,
                    'transferred_at' => now(),
                ]);
            } catch (\Exception $e) {
                $payout->update(['status' => Payout::STATUS_FAILED]);
            }
        }
    }
}
