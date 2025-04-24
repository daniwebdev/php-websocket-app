<?php

namespace IOC\Websocket;

class Route {
    private $routes = [];

    public function onConnect(string $path, array $handler) {
        $this->routes['connect'][$path] = $handler;
    }

    public function add(string $path, string $eventName, array $handler) {
        $this->routes['events'][$eventName][$path] = $handler;
    }

    public function getHandlers(string $eventName, string $path) {
        return $this->routes['events'][$eventName][$path] ?? null;
    }

    public function getConnectHandlers(string $path) {
        return $this->routes['connect'][$path] ?? null;
    }

    public function getRoutes() {
        return $this->routes;
    }
}