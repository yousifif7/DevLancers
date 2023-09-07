<?php

namespace App\Http\Controllers;

use App\Models\Tasks;
use Stripe\Stripe;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;

class StripeController extends Controller
{

    public function checkout()
    {
        return back();
    }

    public function session(Request $request)
    {
        Stripe::setApiKey(config('stripe.sk'));

        $productname = $request->get('productname');
        $totalprice = $request->get('total');
        $sender=$request->get('sender');
        $reciever=$request->get('reciever');
        $two0 = "00";
        $total = "$totalprice$two0";
        $task= Tasks::find($request->task);

        $task->update([
            'payment_flag' => 1,
        ]);

        $session = Session::create([
            'line_items'  => [
                [
                    'price_data' => [
                        'currency'     => 'USD',
                        'product_data' => [
                            "name" => $productname,
                            'description' =>'Payment by '.$sender. ' to '. $reciever. ' for a task.',

                        ],
                        'unit_amount'  => $total,
                    ],
                    'quantity'   => 1,

                ],

            ],
            'mode'        => 'payment',
            'success_url' => route('success'),
            'cancel_url'  => route('checkout'),
        ]);

        return redirect()->away($session->url);
    }

    public function success()
    {
        // return redirect()->route('students.index');
        return back()->with("message","Payment done successfully, Worker will get his payment soon!.");
    }
}
