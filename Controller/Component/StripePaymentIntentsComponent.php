<?php

App::uses('Component', 'Controller');

use \Stripe\PaymentIntent;
use \Stripe\Stripe;
use \Stripe\Event;

class StripePaymentIntentsComponent extends Component
{   
    public function __construct($collection)
    {
        parent::__construct($collection);
        $config = $this->GetConfig();
        $mode = $config['mode'];
        Stripe::setApiKey($config['secret'][$mode]);
    }

    public function startup(\Controller $controller)
    {
        parent::startup($controller);
        $this->Controller = $controller;
    }

    private function GetConfig() { return Configure::read('StripePaymentIntents'); }

    /**
     * Creates a PaymentIntent for the given amount and optional arguments
     * @return \Stripe\PaymentIntent
     */
    public function Create($amount, $arguments = []) 
    {
        $arguments = array_merge(
            ['amount' => $amount, 'currency' => $this->GetConfig()['currency']],
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
     * @throws InvalidCallbackException when callback is not callable
     * @throws \UnexpectedValueException when event could not be deserialized
     */
    public function HandleWebhook($rawPostStream = 'php://input')
    {
        $this->WriteLog('BEGIN: Handle webhook');        

        $payload = @file_get_contents($rawPostStream);
        $event = @Event::constructFrom(
            json_decode($payload, true)
        );

        if (empty($event))
            $this->ThrowLogged(new \UnexpectedValueException('Event decoding failed'));
        
        $this->HandleEvent($event);

        $this->WriteLog('END: Handle webhook');        
    }

    public function HandleEvent(Event $event)
    {
        if(empty($event->type))
            $this->ThrowLogged(new \UnexpectedValueException('Event has no type'));
        
        $this->WriteLog('BEGIN: HandleEvent ' . $event->type);
        $config = $this->GetConfig();
        if (!empty($config['callback']))
        {
            $controller = &$this->Controller;
            call_user_func_array([$controller, $config['callback']], [$event]);
        }

        $this->WriteLog('END: HandleEvent ' . $event->type);
    }

    private function WriteLog($message)
    {
        if (!$this->GetConfig()['logging'])
            return;

        CakeLog::write('stripe_pi', $message);
    }

    private function ThrowLogged($ex)
    {
        $this->WriteLog($ex->getMessage());
        throw $ex;
    }
}