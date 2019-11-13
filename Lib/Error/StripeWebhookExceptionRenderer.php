<?php

App::uses('ExceptionRenderer', 'Error');

class StripeWebhookExceptionRenderer extends ExceptionRenderer {
    public function badRequest($error) {
        echo $error->getMessage();
    }
}