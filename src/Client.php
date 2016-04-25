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

    public function consume($queue, $exchange, $routingKey, $callback)
    {
        $queue = $this->channel->queue_declare($queue, false, false, false, false);
        $this->channel->queue_bind($queue, $exchange, $routingKey);
        $this->channel->basic_consume($queue, '', false, false, false, false, function ($amqpMessage) use($callback) {
            $message = new Message($amqpMessage);
            $callback($message);
        });
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
        $this->channel->basic_publish(new AMQPMessage($message), $exchange, $routingKey);
    }
}
