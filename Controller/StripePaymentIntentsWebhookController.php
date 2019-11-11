<?php

App::uses('AppController', 'Controller');

/**
 * @property StripePaymentIntentsComponent StripePaymentIntents
 */
class StripePaymentIntentsWebhookController extends AppController
{
    public $components = ['StripePaymentIntents.StripePaymentIntents'];

    public function index()
    {
        try
        {
            $this->StripePaymentIntents->HandleWebhook();
        }
        catch(\Exception $e)
        {
            throw new BadRequestException($e->getMessage(), 400);
        }
        
        $this->set('contents', 'OK');
    }
}