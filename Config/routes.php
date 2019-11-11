<?php

Router::connect('/StripePaymentIntents/Webhook', [
    'plugin' => 'StripePaymentIntents',
    'controller' => 'StripePaymentIntentsWebhook',
]);