<?php

namespace StripePaymentIntents\Lib\Error;

use Cake\Error\ExceptionRenderer;

class StripeWebhookExceptionRenderer extends ExceptionRenderer {
    public function badRequest($error) {
        $this->controller->response->statusCode($error->getCode());
        $this->controller->layout = 'plain';
        $this->controller->set('message', $error->getMessage());
        $this->_outputMessage('plain');
    }
}