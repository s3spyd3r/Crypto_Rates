<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Routing\RouteCollectorProxy as Group;
use Slim\Routing\RouteContext;

require_once '../../library/config.php';
require_once 'vendor/autoload.php';
require_once 'RateService.php';

$currencies = require 'currencies.php';
$rateService = new RateService(__DIR__ . '/json', $currencies);

$app = \Slim\Factory\AppFactory::create();
$app->setBasePath(rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/'));
$app->addRoutingMiddleware();

$errorMiddleware = $app->addErrorMiddleware(true, true, true);

$app->group('/{crypto}', function (Group $group) use ($rateService) {
    $group->get('/rates', function (Request $request, Response $response, array $args) use ($rateService): Response {
        try {
            $rates = $rateService->getRates($args['crypto']);
            $response->getBody()->write(json_encode($rates));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => true, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    });

    $group->get('/rates/{currency}', function (Request $request, Response $response, array $args) use ($rateService): Response {
        try {
            $rates = $rateService->getRates($args['crypto'], $args['currency']);
            $response->getBody()->write(json_encode($rates));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => true, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    });

    $group->get('/calculate/{amount}/{currency}', function (Request $request, Response $response, array $args) use ($rateService): Response {
        try {
            $amount = (float) $args['amount'];
            if ($amount <= 0) {
                throw new \InvalidArgumentException('Amount must be a positive number');
            }
            $calculation = $rateService->calculate($args['crypto'], $amount, $args['currency']);
            $response->getBody()->write(json_encode($calculation));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => true, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    });
})->add(function (Request $request, RequestHandler $handler): Response {
    $routeContext = RouteContext::fromRequest($request);
    $crypto = $routeContext->getRoute()->getArgument('crypto');
    if (!in_array(strtoupper($crypto), SUPPORTED_CRYPTOS)) {
        $response = new \Slim\Psr7\Response();
        $response->getBody()->write(json_encode(['error' => true, 'message' => 'Unsupported cryptocurrency']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }
    return $handler->handle($request);
});

$app->run();
