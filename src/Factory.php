<?php

namespace RabbitMQWrapper;

class Factory
{
    /** @var string */
    private $host;

    /** @var string */
    private $port;

    /** @var string */
    private $username;

    /** @var string */
    private $password;

    /**
     * Factory constructor.
     *
     * @param string $host
     * @param string $port
     * @param string $username
     * @param string $password
     */
    public function __construct($host, $port, $username, $password)
    {
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * @return Client
     */
    public function connect()
    {
        $client = new Client();
        $client->connect(
            $this->host,
            $this->port,
            $this->username,
            $this->password
        );

        return $client;
    }
}
