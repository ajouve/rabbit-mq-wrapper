<?php

namespace RabbitMQWrapper;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Class Client
 * @package RabbitMQWrapper
 */
class Client
{

    /** @var AMQPStreamConnection */
    private $connection;

    /** @var AMQPChannel */
    private $channel;

    /**
     * Create a connection to the RabbitMQ client
     *
     * @param string $host
     * @param int    $port
     * @param string $username
     * @param string $password
     */
    public function connect($host = 'localhost', $port = 5672, $username = 'guest', $password = 'guest')
    {
        $this->connection = new AMQPStreamConnection($host, $port, $username, $password);
        $this->channel = $this->connection->channel();
    }

    /**
     * Close the connection with the RabbitMQ client
     */
    public function close()
    {
        $this->channel->close();
        $this->connection->close();
    }

    /**
     * @param string $queue
     * @param string $exchange
     * @param string $routingKey
     * @param callable $callback
     */
    public function consume($queue, $exchange, $routingKey, $callback)
    {
        $this->channel->exchange_declare($exchange, 'topic', false, false, false);
        $this->channel->queue_declare($queue, false, false, false, false);
        $this->channel->queue_bind($queue, $exchange, $routingKey);
        $this->channel->basic_consume($queue, '', false, false, false, false, function ($amqpMessage) use($callback) {
            $message = new Message($amqpMessage);
            $callback($message);
        });

        while(count($this->channel->callbacks)) {
            $this->channel->wait();
        }
    }

    /**
     * Plulish a new message into an exchange
     *
     * @param string $message
     * @param string $exchange
     * @param string $routingKey
     */
    public function publish($message, $exchange, $routingKey = '')
    {
        $this->channel->exchange_declare($exchange, 'fanout', false, false, false);
        $this->channel->basic_publish(
            new AMQPMessage($message, ['content_type' => 'application/json']),
            $exchange,
            $routingKey
        );
    }
}
