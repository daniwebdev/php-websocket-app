<?php

namespace IOC\Websocket\Events;

/**
 * Event Dispatcher for WebSocket server events
 */
class EventDispatcher
{
    /**
     * @var array<string, callable[]> Array of event listeners
     */
    private array $listeners = [];

    /**
     * Register an event listener
     */
    public function on(string $event, callable $callback): void
    {
        if (!isset($this->listeners[$event])) {
            $this->listeners[$event] = [];
        }
        
        $this->listeners[$event][] = $callback;
    }

    /**
     * Dispatch an event with optional payload
     */
    public function dispatch(string $event, mixed ...$args): void
    {
        if (!isset($this->listeners[$event])) {
            return;
        }

        foreach ($this->listeners[$event] as $listener) {
            call_user_func_array($listener, $args);
        }
    }

    /**
     * Remove all listeners for an event
     */
    public function removeListeners(string $event): void
    {
        unset($this->listeners[$event]);
    }
}