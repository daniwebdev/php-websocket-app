<?php
namespace IOC\Websocket;

use WebSocket\Connection;
use WebSocket\Server;
use IOC\Websocket\Handlers\EventHandler;
use IOC\Websocket\Router\Router;

class Websocket {
    private Server $server;
    private EventHandler $eventHandler;
    private Router $router;

    public function __construct(int $port=80) {
        $this->server = new Server($port);
        $this->eventHandler = new EventHandler();
        $this->router = new Router();
    }

    public function start(): void {
        $this->server->onHandshake(function ($server, $connection) {
            $request = $connection->getHandshakeRequest();
            $path = $request->getUri()->getPath();
            if ($this->handleConnect($path, $connection)) {
                echo "New connection: {$connection->getRemoteName()} on path: {$path}\n";
            } else {
                $connection->close();
                echo "Connection rejected for path: {$path}\n";
            }
        });

        $this->server->onText(function ($server, $connection, $message) {
            $this->handleMessage($connection, $message);
        });

        $this->server->onClose(function ($server, $connection) {
            echo "Connection closed: {$connection->getRemoteName()}\n";
        });

        $this->server->start();
    }

    private function handleConnect($path, $connection): bool {
        $handlers = $this->router->getConnectHandlers($path);
        if (!$handlers) {
            return false;
        }

        [$handler, $method] = $handlers;
        if (class_exists($handler)) {
            $instance = new $handler();
            if (method_exists($instance, $method)) {
                return $instance->$method($connection);
            }
        }
        return false;
    }

    private function handleMessage(Connection $connection, string $message): void {
        $data = json_decode($message, true);
        if (!isset($data['event']) || !isset($data['payload'])) {
            return;
        }

        $request = $connection->getHandshakeRequest();
        $path = $request->getUri()->getPath();
        $event = $data['event'];

        $handlers = $this->router->getHandlers($event, $path);
        if (!$handlers) {
            return;
        }

        [$handler, $method] = $handlers;
        if (class_exists($handler)) {
            $instance = new $handler();
            if (method_exists($instance, $method)) {
                $instance->$method($this->server, $connection, $data['payload']);
            }
        }
    }

    public function routeLoader(Router $routes): void {
        $this->router = $routes;
    }
}