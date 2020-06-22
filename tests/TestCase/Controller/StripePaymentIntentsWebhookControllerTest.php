<?php

namespace StripePaymentIntents\TestCase\Controller;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

class StripePaymentIntentsWebhookControllerTest extends TestCase
{
    use IntegrationTestTrait;

    public function setUp(): void
    {
        parent::setUp();
        $this->component = $this->getMockBuilder('StripePaymentIntents\Controller\Component\StripePaymentIntentsComponent')
            ->setMethods(['GetMode', 'HandleWebhook'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->component->expects($this->once())
            ->method('GetMode')
            ->will($this->returnValue('test'));
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
        $this->post('/StripePaymentIntents/Webhook');
    }
}