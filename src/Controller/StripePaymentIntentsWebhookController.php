<?php


/**
 * @property StripePaymentIntentsComponent StripePaymentIntents
 */
namespace StripePaymentIntents\Controller;

use Cake\Event\Event;

class StripePaymentIntentsWebhookController extends AppController
{
    public $components = ['StripePaymentIntents.StripePaymentIntents'];

    // https://stripe.com/files/ips/ips_webhooks.txt
    static $stripeWebhookIps = [
        '54.187.174.169',
        '54.187.205.235',
        '54.187.216.72',
        '54.241.31.99',
        '54.241.31.102',
        '54.241.34.107'];

    function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);        
        if ($this->StripePaymentIntents->GetMode() == 'test' || in_array($_SERVER['REMOTE_ADDR'], self::$stripeWebhookIps))
            Configure::write('Exception.renderer', 'StripePaymentIntents.StripeWebhookExceptionRenderer');
    }

    function beforeRender(Event $event) {
        parent::beforeRender($event);
        $this->viewBuilder()->setLayout('plain');
    }

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