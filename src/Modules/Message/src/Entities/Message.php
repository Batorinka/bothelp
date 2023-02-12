<?php

declare(strict_types=1);

namespace BotHelp\WebApi\Modules\Message\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\UuidInterface;

/**
 * @property UuidInterface $id
 * @property int $account_id
 * @property boolean $is_consumed
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Message extends Model
{
    protected $casts = [
        'account_id' => 'int',
        'is_consumed' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

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

    public function getCreatedAt(): Carbon
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): Carbon
    {
        return $this->updated_at;
    }
}
