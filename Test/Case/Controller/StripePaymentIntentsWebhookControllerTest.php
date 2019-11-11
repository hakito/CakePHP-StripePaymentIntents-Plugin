<?php

class StripePaymentIntentsWebhookControllerTest extends ControllerTestCase
{
        /** @var StripePaymentIntentsWebhookController controller */
        public $Controller = null;
    
        public function setUp()
        {
            $this->Controller = $this->generate('StripePaymentIntents.StripePaymentIntentsWebhook',
                    [
                        'methods' => array ('webhook'),
                        'components' => array('StripePaymentIntents.StripePaymentIntents')                    
                    ]);
        }

        public function testIndexCallsComponent()
        {
            $target = $this->Controller->StripePaymentIntents;
            $target->expects($this->once())
                     ->method('HandleWebhook');
            $this->testAction('/StripePaymentIntents/Webhook');
        }

        
}