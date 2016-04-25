<?php

namespace RabbitMQWrapper;

use PhpAmqpLib\Message\AMQPMessage;

class Message
{

    /** @var AMQPMessage */
    private $amqpMessage;

    public function __construct(AMQPMessage $message)
    {
        $this->amqpMessage = $message;
    }

    public function getContent()
    {
        return $this->amqpMessage->body;
    }

    public function nack($reQueue = false)
    {
        $this->amqpMessage->delivery_info['channel']->basic_nack($this->amqpMessage->delivery_info['delivery_tag'], false, $reQueue);
    }

    public function ack()
    {
        $this->amqpMessage->delivery_info['channel']->basic_ack($this->amqpMessage->delivery_info['delivery_tag']);
    }
}
