<?php

use App\Handlers\ChatHandler;
use IOC\Websocket\Router\Router;

include __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Initialize the WebSocket server
$websocket = new IOC\Websocket\Websocket(8123);

$routes = new Router();

$routes->onConnect('/chat', [ChatHandler::class, 'onConnect']);
$routes->add('/chat', 'chat.message', [ChatHandler::class, 'onMessage']);

$websocket->routeLoader($routes);

$websocket->start();