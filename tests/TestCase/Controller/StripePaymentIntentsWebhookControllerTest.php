<?php

namespace StripePaymentIntents\TestCase\Controller;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

use StripePaymentIntents\Controller\StripePaymentIntentsWebhookController;

class StripePaymentIntentsWebhookControllerTest extends TestCase
{
    use IntegrationTestTrait;

    public function setUp()
    {
        // $this->Controller = $this->generate('StripePaymentIntents.StripePaymentIntentsWebhook',
        //     [
        //         'methods' => [],
        //         'components' => ['StripePaymentIntents.StripePaymentIntents' => ['GetMode', 'HandleWebhook']]
        //     ]);
        // $this->Controller->StripePaymentIntents
        //     ->expects($this->once())
        //     ->method('GetMode')
        //     ->will($this->returnValue('test'));
        $this->component = $this->getMockBuilder('StripePaymentIntents\Controller\Component\StripePaymentIntentsComponent')
            ->setMethods(['GetMode', 'HandleWebhook'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function controllerSpy($event, $controller = null)
    {
        /* @var $controller StripePaymentIntentsWebhookController */
        $controller = $event->getSubject();
        $controller->StripePaymentIntents = $this->component;            
    }    

    public function testIndexCallsComponent()
    {
        $this->component->expects($this->once())
                    ->method('HandleWebhook');
        $this->get('/StripePaymentIntents/Webhook');
    }        
}