<?php

declare(strict_types=1);

namespace BotHelp\WebApi\Modules\Message\Repositories;

use BotHelp\WebApi\Modules\Message\Exceptions\CantStoreException;
use BotHelp\WebApi\Modules\Message\Exceptions\CantUpdateException;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

interface WriteMessageRepositoryInterface
{
    /**
     * @throws CantStoreException
     */
    public function store(int $accountId): UuidInterface;

    /**
     * @throws CantUpdateException
     */
    public function markAsConsumed(Uuid $id): void;
}
