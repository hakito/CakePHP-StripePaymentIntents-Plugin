<?php

Configure::write('StripePaymentIntents', [
    'mode'=> 'test',
    'currency' => 'eur',
    'secret' => [
        'test' => 'sk_test_4eC39HqLyjWDarjtT1zdp7dc',
        'live' => 'pk_live_4eC39HqLyjWDarjtT1zdp7dc'
    ],
    'logging' => false,

    // optional
    'callback' => 'stripeWebhookCallback', // Name of callback function for stripe events to be called in app controller
]);