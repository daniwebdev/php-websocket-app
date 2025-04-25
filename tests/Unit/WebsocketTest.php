<?php

use PHPUnit\Framework\TestCase;
use Phocket\Framework\Websocket;

class WebsocketTest extends TestCase
{
    protected $websocket;

    protected function setUp(): void
    {
        $this->websocket = new Websocket();
    }

    public function testServerStarts()
    {
        $this->assertTrue($this->websocket->start());
    }

    public function testHandleConnection()
    {
        $connection = $this->websocket->handleConnection();
        $this->assertNotNull($connection);
        $this->assertTrue($connection->isConnected());
    }

    public function testProcessMessage()
    {
        $message = "Hello, World!";
        $response = $this->websocket->processMessage($message);
        $this->assertEquals("Message processed: Hello, World!", $response);
    }
}