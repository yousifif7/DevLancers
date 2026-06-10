<?php
 
return [
    'pk' => env('STRIPE_KEY'),
    'sk' => env('STRIPE_SECRET'),
    'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    'connect_enabled' => env('STRIPE_CONNECT_ENABLED', false),
];