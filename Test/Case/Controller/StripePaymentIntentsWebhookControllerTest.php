<?php

class StripePaymentIntentsWebhookControllerTest extends ControllerTestCase
{
        /** @var StripePaymentIntentsWebhookController controller */
        public $Controller = null;
    
        public function setUp()
        {
            $this->Controller = $this->generate('StripePaymentIntents.StripePaymentIntentsWebhook',
                [
                    'methods' => [],
                    'components' => ['StripePaymentIntents.StripePaymentIntents' => ['GetMode', 'HandleWebhook']]
                ]);
            $this->Controller->StripePaymentIntents
                ->expects($this->once())
                ->method('GetMode')
                ->will($this->returnValue('test'));
        }

        public function testIndexCallsComponent()
        {
            $target = $this->Controller->StripePaymentIntents;
            $target->expects($this->once())
                     ->method('HandleWebhook');
            $this->testAction('/StripePaymentIntents/Webhook');
        }

        
}