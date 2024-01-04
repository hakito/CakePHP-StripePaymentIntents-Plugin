<?php


/**
 * @property StripePaymentIntentsComponent StripePaymentIntents
 */
namespace StripePaymentIntents\Controller;

use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Http\Exception\BadRequestException;
use StripePaymentIntents\Controller\Component\StripePaymentIntentsComponent;

/**
 * @property StripePaymentIntentsComponent StripePaymentIntents
 * @package StripePaymentIntents\Controller
 */
class StripePaymentIntentsWebhookController extends AppController
{
    // https://stripe.com/files/ips/ips_webhooks.txt
    static $stripeWebhookIps = [
        '3.18.12.63',
        '3.130.192.231',
        '13.235.14.237',
        '13.235.122.149',
        '18.211.135.69',
        '35.154.171.200',
        '52.15.183.38',
        '54.88.130.119',
        '54.88.130.237',
        '54.187.174.169',
        '54.187.205.235',
        '54.187.216.72'];

    function initialize(): void
    {
        $this->loadComponent('StripePaymentIntents.StripePaymentIntents');
    }

    function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        if ($this->StripePaymentIntents->GetMode() == 'test' || in_array($_SERVER['REMOTE_ADDR'], self::$stripeWebhookIps))
            Configure::write('Exception.renderer', 'StripePaymentIntents.StripeWebhookExceptionRenderer');
    }

    function beforeRender(EventInterface $event) {
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