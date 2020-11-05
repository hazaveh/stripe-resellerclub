<?php

namespace App;

use DI\Container;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;

class StripeCallbackMiddleware {


    private $container;
    private $status;

    public function __construct(string $status, Container $container) {
        $this->container = $container;
        $this->status = $status;
    }

    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler) {

        if (!array_key_exists('redirectUrl', $_SESSION) && !array_key_exists('transid', $_SESSION)) {
            $response = new Response(400);
            return $this->container->get('view')->render($response, 'error.php');
        }

        return $handler->handle($request);

    }


}

