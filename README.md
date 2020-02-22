[![Build Status](https://travis-ci.org/hakito/CakePHP-StripePaymentIntents-Plugin.svg?branch=master)](https://travis-ci.org/hakito/CakePHP-StripePaymentIntents-Plugin)
[![Coverage Status](https://coveralls.io/repos/github/hakito/CakePHP-StripePaymentIntents-Plugin/badge.svg?branch=master)](https://coveralls.io/github/hakito/CakePHP-StripePaymentIntents-Plugin?branch=master)
[![Latest Stable Version](https://poser.pugx.org/hakito/cakephp-stripe-payment-intents-plugin/v/stable)](https://packagist.org/packages/hakito/cakephp-stripe-payment-intents-plugin)
[![Latest Unstable Version](https://poser.pugx.org/hakito/cakephp-stripe-payment-intents-plugin/v/unstable)](https://packagist.org/packages/hakito/cakephp-stripe-payment-intents-plugin)
[![License](https://poser.pugx.org/hakito/cakephp-stripe-payment-intents-plugin/license)](https://packagist.org/packages/hakito/cakephp-stripe-payment-intents-plugin)

# CakePHP-StripePaymentIntents-Plugin
CakePHP plugin for Stripe Payment Intents

Installation
------------

### Using composer

If you are using composer simply add it using the following command:

```sh
composer require hakito/cakephp-stripe-payment-intents-plugin
```

### Without composer

Download the plugin to app/Plugin/StripePaymentIntents.

Confguration
------------

Load the Plugin in your bootstrap.php

```php
CakePlugin::load('StripePaymentIntents', array('routes' => true));

// If you want to collect the log stream configure a logging scope for 'stripe_pi':
CakeLog::config('stripe_pi', array(
	'engine' => 'FileLog',
	'scopes' => array('stripe_pi'),
));

```

Add the following config to your core.php

```php
Configure::write('StripePaymentIntents', [
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

    // optional
    'callback' => 'stripeWebhookCallback', // Name of callback function for stripe events to be called in app controller
]);
```

Usage
-----

Add the plugin component to your controller:

```php
public $components = ['StripePaymentIntents.StripePaymentIntents'];
```

Create or retrieve a payment intent for the checkout process

```php
// create new
$pi = $this->StripePaymentIntents->Create(1234, ['metadata' => ['order_id' => $orderId]]); // 12.34
// or update shopping cart
$pi = $this->StripePaymentIntents->Retrieve('pi_xyz');
```

Set the view data

```php
$this->set('StripeClientSecret', $pi->client_secret);
$this->set('StripePublicKey', $this->StripePaymentIntents->GetPublicKey());
```

Implement the view behavior according to [stripe documentation](https://stripe.com/docs/payments/accept-a-payment#web-collect-card-details).

### Webhook callback

You have to implement the webhook callback function in your AppController:

```php
/**
* @param \Stripe\Event $event
*/
public function stripeWebhookCallback($event)
{
    if ($event->type == 'payment_intent.succeeded')
    {
       // handle success
    }
}
```
