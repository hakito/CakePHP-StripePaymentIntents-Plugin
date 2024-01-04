<?php

namespace StripePaymentIntents\TestCase\Controller;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class StripePaymentIntentsWebhookControllerTest extends TestCase
{
    use IntegrationTestTrait;

    private MockObject $component;

    public function setUp(): void
    {
        parent::setUp();
        $this->component = $this->getMockBuilder('StripePaymentIntents\Controller\Component\StripePaymentIntentsComponent')
            ->onlyMethods(['GetMode', 'HandleWebhook'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->component->expects($this->once())
            ->method('GetMode')
            ->willReturn('test');
    }

    public function controllerSpy($event, $controller = null)
    {
        /** @var $controller StripePaymentIntentsWebhookController */
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