[![Build Status](https://app.travis-ci.com/hakito/CakePHP-StripePaymentIntents-Plugin.svg?branch=master)](https://app.travis-ci.com/hakito/CakePHP-StripePaymentIntents-Plugin)
[![Coverage Status](https://coveralls.io/repos/github/hakito/CakePHP-StripePaymentIntents-Plugin/badge.svg?branch=master)](https://coveralls.io/github/hakito/CakePHP-StripePaymentIntents-Plugin?branch=master)
[![Latest Stable Version](https://poser.pugx.org/hakito/cakephp-stripe-payment-intents-plugin/v/stable)](https://packagist.org/packages/hakito/cakephp-stripe-payment-intents-plugin)
[![Latest Unstable Version](https://poser.pugx.org/hakito/cakephp-stripe-payment-intents-plugin/v/unstable)](https://packagist.org/packages/hakito/cakephp-stripe-payment-intents-plugin)
[![License](https://poser.pugx.org/hakito/cakephp-stripe-payment-intents-plugin/license)](https://packagist.org/packages/hakito/cakephp-stripe-payment-intents-plugin)

# CakePHP-StripePaymentIntents-Plugin
CakePHP 4.x plugin for Stripe Payment Intents

## Installation
------------

### Using composer

If you are using composer simply add it using the following command:

```sh
composer require hakito/cakephp-stripe-payment-intents-plugin
```

### Without composer

Download the plugin to app/Plugin/StripePaymentIntents.

### Load the plugin

Load the Plugin in your Application.php

```php
public function bootstrap(): void
{
    // Call parent to load bootstrap from files.
    parent::bootstrap();

    $this->addPlugin(\StripePaymentIntents\Plugin::class, ['routes' => true]);
}
```

## Confguration

Add the following config to your app.php

```php
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
```

You can also setup logging

```php
'Log' =>
[
    'stripe' =>
    [
        'className' => FileLog::class,
        'path' => LOGS,
        'file' => 'stripe',
        'scopes' => ['Stripe'],
        'levels' => ['warning', 'error', 'critical', 'alert', 'emergency', 'info'],
    ],
]
```

## Usage

In your payment handling controller:

```php
// Load the component
public function initialize(): void
{
    parent::initialize();
    $this->loadComponent('StripePaymentIntents.StripePaymentIntents');
}
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

## Stripe webhook events

You have to handle stripe events implementing an event handler:

```php
\Cake\Event\EventManager::instance()->on('StripePaymentIntents.Event',
function (\Cake\Event\Event $event, \Stripe\Event $stripeEvent)
{
    return ['handled' => true]; // If you don't set the handled flag to true
                                // the plugin will throw an exception
});
```
