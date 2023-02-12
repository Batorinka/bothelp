<?php

declare(strict_types=1);

namespace BotHelp\WebApi\Modules\Message\Dto;

use Ramsey\Uuid\UuidInterface;

class MessageDto
{
    public function __construct(
        private UuidInterface $id,
        private int $account_id,
        private bool $is_consumed,
    ) {
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getAccountId(): int
    {
        return $this->account_id;
    }

    public function isConsumed(): bool
    {
        return $this->is_consumed;
    }
}
