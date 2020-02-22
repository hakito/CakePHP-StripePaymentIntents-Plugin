<?php

use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

Router::plugin(
    'StripePaymentIntents',
    ['path' => '/StripePaymentIntents'], 
    function (RouteBuilder $routes) {
        $routes->get('/Webhook',
            [
                'controller' => 'StripePaymentIntentsWebhook',
            ]
        );
    }
);