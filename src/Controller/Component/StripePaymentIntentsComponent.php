<?php

namespace StripePaymentIntents\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;

use Stripe\PaymentIntent;
use Stripe\Stripe;

class StripePaymentIntentsComponent extends Component
{
    private $publicKey;
    public function GetPublicKey() { return $this->publicKey; }

    private $mode;
    public function GetMode() { return $this->mode; }

    public function __construct($collection)
    {
        parent::__construct($collection);
        $config = $this->_GetConfig();
        $this->mode = $config['mode'];
        $keys = &$config['keys'][$this->mode];
        Stripe::setApiKey($keys['secret']);
        $this->publicKey = $keys['public'];
    }

    public function startup($event)
    {
        $this->Controller = $event->getSubject();
    }

    private function _GetConfig() { return Configure::read('StripePaymentIntents'); }

    /**
     * Creates a PaymentIntent for the given amount and optional arguments
     * @return \Stripe\PaymentIntent
     */
    public function Create($amount, $arguments = [])
    {
        $arguments = array_merge(
            ['amount' => $amount, 'currency' => $this->_GetConfig()['currency']],
            $arguments
        );

        return PaymentIntent::create($arguments);
    }

    /**
     * @param string $intent Id of the intent
     * @return \Stripe\PaymentIntent
     */
    public function Retrieve($intent)
    {
        return PaymentIntent::retrieve($intent);
    }

    /**
     * Handles the Stripe webhook
     * @param string $rawPostStream will read from this stream or file with file_get_contents
     * @throws \UnexpectedValueException when event could not be deserialized
     */
    public function HandleWebhook($rawPostStream = 'php://input')
    {
        $this->WriteLog('BEGIN: Handle webhook');

        $payload = @file_get_contents($rawPostStream);
        $event = @\Stripe\Event::constructFrom(
            json_decode($payload, true)
        );

        if (empty($event))
            $this->ThrowLogged(new \UnexpectedValueException('Event decoding failed'));

        $this->HandleEvent($event);

        $this->WriteLog('END: Handle webhook');
    }

    public function HandleEvent(\Stripe\Event $event)
    {
        if(empty($event->type))
            $this->ThrowLogged(new \UnexpectedValueException('Event has no type'));

        $this->WriteLog('BEGIN: HandleEvent ' . $event->type);
        $cakeEvent = new \Cake\Event\Event('StripePaymentIntents.Event', $this,
        [
            'stripeEvent' => $event
        ]);
        $this->Controller->getEventManager()->dispatch($cakeEvent);

        try
        {
            $result = $cakeEvent->getResult();
            if(empty($result['handled']))
                throw new \Exception('Stripe event ' . $event->type . ' is unhandled');
        }
        finally
        {
            $this->WriteLog('END: HandleEvent ' . $event->type);
        }
    }

    private function WriteLog($message)
    {
        if (!$this->_GetConfig()['logging'])
            return;

        Log::write('stripe_pi', $message);
    }

    private function ThrowLogged($ex)
    {
        $this->WriteLog($ex->getMessage());
        throw $ex;
    }
}