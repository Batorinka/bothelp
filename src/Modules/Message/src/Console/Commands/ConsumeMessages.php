<?php

declare(strict_types=1);

namespace BotHelp\WebApi\Modules\Message\Console\Commands;

use BotHelp\WebApi\Modules\Message\Queues\Consumer\MessageConsumer;
use BotHelp\WebApi\Modules\Message\Queues\Queue;
use ErrorException;
use Illuminate\Console\Command;

class ConsumeMessages extends Command
{
    private MessageConsumer $consumer;
    private Queue $queue;

    public function __construct(
        MessageConsumer $consumer,
        Queue $queue
    ) {
        parent::__construct();

        $this->consumer = $consumer;
        $this->queue = $queue;
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'message:consume';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Read messages from queue.';

    /**
     * Execute the console command.
     *
     * @throws ErrorException
     */
    public function handle()
    {
        $this->queue->consumeMessages([$this->consumer, 'handle']);

        return Command::SUCCESS;
    }
}
