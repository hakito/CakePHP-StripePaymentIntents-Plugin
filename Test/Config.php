<?php

Configure::write('StripePaymentIntents', [
    'mode'=> 'test',
    'currency' => 'eur',
    'keys' => [
        'test' => [
            'secret' => 'sk_test_4eC39HqLyjWDarjtT1zdp7dc',
            'public' => 'pk_test',
        ],
        'live' => [
            'secret' => 'sk_live_key',
            'public' => 'pk_live_key'
        ]
    ],
    'logging' => false,

    // optional
    'callback' => 'stripeWebhookCallback', // Name of callback function for stripe events to be called in app controller
]);