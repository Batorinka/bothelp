<?php

declare(strict_types=1);

namespace BotHelp\WebApi\Modules\Message\Queues\Consumer;

use BotHelp\WebApi\Modules\Message\Exceptions\CantFindException;
use BotHelp\WebApi\Modules\Message\Exceptions\CantUpdateException;
use BotHelp\WebApi\Modules\Message\Exceptions\QueueOrderException;
use BotHelp\WebApi\Modules\Message\Repositories\ReadMessageRepositoryInterface;
use BotHelp\WebApi\Modules\Message\Repositories\WriteMessageRepositoryInterface;
use Illuminate\Support\Facades\Log;
use JsonException;
use PhpAmqpLib\Channel\AMQPChannel;

class MessageConsumer
{
    private const QUEUE_ORDER_EXCEPTION = 'Sequence is broken';
    private WriteMessageRepositoryInterface $writeRepository;
    private ReadMessageRepositoryInterface $readRepository;

    public function __construct(
        WriteMessageRepositoryInterface $writeRepository,
        ReadMessageRepositoryInterface $readRepository,
    ) {
        $this->writeRepository = $writeRepository;
        $this->readRepository = $readRepository;
    }

    /**
     * @throws JsonException
     */
    public function handle($msg): void
    {
        /** @var AMQPChannel $channel */
        $channel = $msg->delivery_info['channel'];

        $data = json_decode($msg->body, true, 512, JSON_THROW_ON_ERROR);

        try {
            $message = $this->readRepository->getFirstNotConsumedMessageByAccountId($data['account_id']);

            if ($message->getId()->toString() !== $data['id']) {
                throw new QueueOrderException(self::QUEUE_ORDER_EXCEPTION);
            }

            sleep(1);

            $this->writeRepository->markAsConsumed($message->getId());

            $channel->basic_ack($msg->delivery_info['delivery_tag']);
        } catch (QueueOrderException|CantUpdateException  $e) {
            Log::error($e->getMessage());
            $channel->basic_nack($msg->delivery_info['delivery_tag'], false, true);
        } catch (CantFindException $e) {
            Log::error($e->getMessage());
        }
    }
}
