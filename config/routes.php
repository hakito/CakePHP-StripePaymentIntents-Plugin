<?php

use Cake\Routing\RouteBuilder;

/** @var \Cake\Routing\RouteBuilder $routes */
$routes->plugin(
    'StripePaymentIntents',
    ['path' => '/StripePaymentIntents'],
    function (RouteBuilder $routes) {
        $routes->post('/Webhook',
            [
                'controller' => 'StripePaymentIntentsWebhook',
            ]
        );
    }
);