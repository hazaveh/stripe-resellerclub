<?php

use App\RCPaymentMiddleware;
use App\RCPostPaymentPayload;
use App\StripeCallbackMiddleware;
use App\StripeSession;
use Psr\Http\Message\ServerRequestInterface as RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use DI\Container;
use Slim\Views\TwigMiddleware;
use Stripe\Stripe;

require __DIR__ . '/vendor/autoload.php';


$container = new Container();

$bootstrap = require __DIR__ . '/src/includes/bootstrap.php';

AppFactory::setContainer($container);

$bootstrap($container);

$app = AppFactory::create();
session_start();
$app->add(TwigMiddleware::createFromContainer($app));
$app->addErrorMiddleware($app->getContainer()->get('settings')['displayErrorDetails'], $app->getContainer()->get('settings')['logErrors'], $app->getContainer()->get('settings')['logErrorDetails']);

$app->add(function($request, $handler) {

    Stripe::setApiKey($this->get('settings')['stripe_secret']);
    return $handler->handle($request);

});

$app->get('/', function (RequestInterface $request, ResponseInterface $response, $args) {

    $session = (new StripeSession($request))->session;

    return $this->get('view')->render($response, 'prepayment.php', [
        "sessionId" => $session->id,
        "company" => $this->get('settings')['name'],
        "stripe_key" => $this->get('settings')['stripe_publish_key']
    ]);

})->add(new RCPaymentMiddleware($app->getContainer()));

$app->get('/create-session', function (RequestInterface $request, ResponseInterface $response){

    $session = new StripeSession($request);

    return $response->withJson([ 'id' => $session->id ])->withStatus(200);
});

$app->get('/success', function(RequestInterface $request, ResponseInterface $response){

    $payload = new RCPostPaymentPayload($this, 'Y');

    return $this->get('view')->render($response, 'postpayment.php', $payload->toArray());

})->add(new StripeCallbackMiddleware('Y', $app->getContainer()));

$app->get('/error', function(RequestInterface $request, ResponseInterface $response){

    $payload = new RCPostPaymentPayload($this, 'N');

    return $this->get('view')->render($response, 'postpayment.php', $payload->toArray());

})->add(new StripeCallbackMiddleware('N', $app->getContainer()));

$app->run();