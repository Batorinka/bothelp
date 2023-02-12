<?php
declare(strict_types=1);

namespace BotHelp\WebApi\Modules\Message\Queues;

use ErrorException;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Queue
{
    private const QUEUE = 'bot_help_messages';

    private AMQPStreamConnection $connection;
    private AMQPChannel $channel;

    public function __construct()
    {
        $this->connection = new AMQPStreamConnection(
            'bothelp_rabbitmq',
            5672,
            'default',
            'default'
        );

        $this->channel = $this->connection->channel();
    }

    public function publishMessage(string $data): void
    {
        $this->channel->queue_declare(
            self::QUEUE,
            false,
            true,
            false,
            false
        );

        $msg = new AMQPMessage($data);
        $this->channel->basic_publish($msg, '', self::QUEUE);
    }

    /**
     * @throws ErrorException
     */
    public function consumeMessages(callable $callback): void
    {
        $this->channel->basic_qos(
            null,
            10,
            null
        );
        $this->channel->basic_consume(
            self::QUEUE,
            '',
            false,
            false,
            false,
            false,
            $callback
        );

        while ($this->channel->is_open) {
            $this->channel->wait();
        }
    }

    public function __destruct()
    {
        $this->channel->close();
        $this->connection->close();
    }
}
