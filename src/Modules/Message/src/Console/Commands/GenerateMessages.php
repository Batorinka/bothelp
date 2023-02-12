<?php

declare(strict_types=1);

namespace BotHelp\WebApi\Modules\Message\Console\Commands;

use BotHelp\WebApi\Modules\Message\Exceptions\CantStoreException;
use BotHelp\WebApi\Modules\Message\Queues\Queue;
use BotHelp\WebApi\Modules\Message\Repositories\WriteMessageRepositoryInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use JsonException;

class GenerateMessages extends Command
{
    private WriteMessageRepositoryInterface $repository;
    private Queue $queue;

    public function __construct(
        WriteMessageRepositoryInterface $writeRepository,
        Queue $queue
    ) {
        parent::__construct();

        $this->repository = $writeRepository;
        $this->queue = $queue;
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'message:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate 10000 messages and publish to queue.';

    /**
     * Execute the console command.
     *
     * @return int
     * @throws JsonException
     */
    public function handle(): int
    {
        $index = 0;
        while ($index < 10_000) {
            try {
                DB::beginTransaction();

                $accountId = $this->getRandomAccountId();

                $messageId = $this->repository->store($accountId);

                $message = [
                    'id' => $messageId->toString(),
                    'account_id' => $accountId,
                ];

                $this->queue->publishMessage(
                    json_encode($message, JSON_THROW_ON_ERROR)
                );

                DB::commit();
            } catch (CantStoreException $e) {
                $this->error($e->getMessage());
                DB::rollBack();
            }

            $index++;
        }

        return Command::SUCCESS;
    }

    private function getRandomAccountId(): int
    {
        return mt_rand(1, 1000);
    }
}
