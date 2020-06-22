<?php

namespace StripePaymentIntents\Test\TestApp;

use Cake\Http\BaseApplication;
use Cake\Http\MiddlewareQueue;
use Cake\Routing\Middleware\RoutingMiddleware;

class Application extends BaseApplication
{

    /**
     * {@inheritDoc}
     */
    public function bootstrap(): void
    {
        // Call parent to load bootstrap from files.
        parent::bootstrap();

        $this->addPlugin(\StripePaymentIntents\Plugin::class, ['routes' => true]);
    }

    /**
     * Setup the middleware queue your application will use.
     *
     * @param MiddlewareQueue $middlewareQueue The middleware queue to setup.
     * @return MiddlewareQueue The updated middleware queue.
     */
    public function middleware(MiddlewareQueue $middlewareQueue): MiddlewareQueue
    {
        $middlewareQueue
            ->add(new RoutingMiddleware($this));

        return $middlewareQueue;
    }
}
