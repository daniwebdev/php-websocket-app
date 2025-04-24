<?php

namespace IOC\Websocket;

class EventHandler {
    protected $events = [];

    public function register($eventName, callable $handler) {
        if (!isset($this->events[$eventName])) {
            $this->events[$eventName] = [];
        }
        $this->events[$eventName][] = $handler;
    }

    public function trigger($eventName, ...$args) {
        if (isset($this->events[$eventName])) {
            foreach ($this->events[$eventName] as $handler) {
                call_user_func($handler, ...$args);
            }
        }
    }
}