<?php
return [
    'StripePaymentIntents' => [
        'mode'=> 'test',
        'currency' => 'eur',
        'keys' => [
            'test' => [
                'secret' => 'sk_test_4eC39HqLyjWDarjtT1zdp7dc',
                'public' => 'pk_test_abc',
            ],
            'live' => [
                'secret' => 'sk_live_key',
                'public' => 'pk_live_key'
            ]
        ],
        'logging' => false,
    ]
];