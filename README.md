# rabbit-mq-wrapper

RabbitMQWrapper is a library to simplify RabbitMQ usage

## Examples

Send a new message

    $client = new \RabbitMQWrapper\Client();
    $client->connect();
    $client->publish('Message !!', 'exchange_example', 'exchange_example.key');
    $client->close();

Receive a message

    $client = new \RabbitMQWrapper\Client();
    $client->connect();
    $client->consume('queue_example', 'exchange_example', 'exchange_example.key', function(\RabbitMQWrapper\Message $message) {
        echo $message->getContent() . "\n";
        $message->ack();
    });
    $client->close();
