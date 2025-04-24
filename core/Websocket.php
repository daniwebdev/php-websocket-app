<?php
namespace IOC\Websocket;

use WebSocket\Connection;
use WebSocket\Server;

class Websocket {
    private Server $server;
    private $eventHandler;
    private $routes;

    public function __construct($port=80) {
        $this->server = new \WebSocket\Server($port);
        $this->eventHandler = new EventHandler();
    }

    public function start() {
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


    private function handleConnect($path, $connection) {
        echo 'ok nice to meet you: ' . $connection->getRemoteName() . "\n";
        print_r($this->routes['connect'][$path]);
        print_r($path);

        if (!isset($this->routes['connect'][$path])) {
            return false;
        }

        [$handler, $method] = $this->routes['connect'][$path];
        if (class_exists($handler)) {
            $instance = new $handler();
            if (method_exists($instance, $method)) {
                return $instance->$method($connection);
            }
        }
        return false;
    }

    private function handleMessage(Connection $connection, $message) {
        $data = json_decode($message, true);
        if (!isset($data['event']) || !isset($data['payload'])) {
            return;
        }

        $request = $connection->getHandshakeRequest();

        $path = $request->getUri()->getPath();
        $event = $data['event'];

        if (!isset($this->routes['events'][$event][$path])) {
            return;
        }


        [$handler, $method] = $this->routes['events'][$event][$path];
        if (class_exists($handler)) {
            $instance = new $handler();
            if (method_exists($instance, $method)) {
                // Pass server instance along with connection and payload
                $instance->$method($this->server, $connection, $data['payload']);
            }
        }
    }

    public function routeLoader($routes) {
        $this->routes = $routes->getRoutes();
    }
}
