# PHP WebSocket Application

## Overview
This project is a WebSocket application built in PHP that provides an event-driven architecture for handling real-time communication. It is designed to facilitate chat functionalities, allowing users to send and receive messages in real-time.

## Project Structure
```
php-websocket-app
├── core
│   ├── EventHandler.php
│   ├── Route.php
│   └── Websocket.php
├── src
│   ├── handlers
│   │   └── ChatHandler.php
│   └── events
│       └── ChatEvents.php  
├── tests
│   └── Unit
│       └── WebsocketTest.php
├── .env
├── .env.example
├── .gitignore
├── bootstrap.php
├── composer.json
├── server.php
└── README.md
```

## Installation
1. Clone the repository:
   ```
   git clone <repository-url>
   ```
2. Navigate to the project directory:
   ```
   cd php-websocket-app
   ```
3. Install dependencies using Composer:
   ```
   composer install
   ```

## Configuration
1. Copy the `.env.example` file to `.env` and update the environment variables as needed.
2. Ensure that the necessary permissions are set for the server to run.

## Usage
To start the WebSocket server, run the following command:
```
php server.php
```

## Features
- Real-time chat functionality
- Event-driven architecture for handling various events
- Modular design with separate classes for handling events, routing, and WebSocket connections

## Testing
Unit tests are provided to ensure the functionality of the WebSocket server. To run the tests, use:
```
vendor/bin/pest
```

## Contributing
Contributions are welcome! Please submit a pull request or open an issue for any enhancements or bug fixes.

## License
This project is licensed under the MIT License. See the LICENSE file for more details.