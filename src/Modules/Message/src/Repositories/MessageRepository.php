<?php

declare(strict_types=1);

namespace BotHelp\WebApi\Modules\Message\Repositories;

use BotHelp\WebApi\Modules\Message\Dto\MessageDto;
use BotHelp\WebApi\Modules\Message\Exceptions\CantFindException;
use BotHelp\WebApi\Modules\Message\Exceptions\CantStoreException;
use BotHelp\WebApi\Modules\Message\Exceptions\CantUpdateException;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\UuidFactory;
use Ramsey\Uuid\UuidInterface;

class MessageRepository implements ReadMessageRepositoryInterface, WriteMessageRepositoryInterface
{
    private const TABLE_NAME = 'messages';
    private const CANT_FOUND_EXCEPTION = 'Can\'t find not consumed message for account id %d';
    private const CANT_STORE_EXCEPTION = 'Can\'t store message for account id %d';
    private const CANT_UPDATE_EXCEPTION = 'Can\'t update message with id %s';


    private UuidFactory $factory;

    public function __construct(UuidFactory $factory)
    {
        $this->factory = $factory;
    }

    public function getFirstNotConsumedMessageByAccountId(int $accountId): MessageDto
    {
        $query = DB::table(self::TABLE_NAME)
            ->where(
                [
                    'account_id' => $accountId,
                    'is_consumed' => false,
                ]
            )
            ->orderByRaw('id')
            ->first();

        if ($query === null) {
            throw new CantFindException(
                sprintf(
                    self::CANT_FOUND_EXCEPTION,
                    $accountId
                )
            );
        }

        return new MessageDto(
            $this->factory->fromString($query->id),
            (int) $query->account_id,
            (bool) $query->is_consumed
        );
    }

    public function store(int $accountId): UuidInterface
    {
        $id = $this->factory->uuid6();

        $query = DB::table(self::TABLE_NAME)
            ->insert(
                [
                    'id' => $id->toString(),
                    'account_id' => $accountId,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]
            );

        if ($query === false) {
            throw new CantStoreException(
                sprintf(
                    self::CANT_STORE_EXCEPTION,
                    $accountId
                )
            );
        }

        return $id;
    }

    public function markAsConsumed(UuidInterface $id): void
    {
        $query = DB::table(self::TABLE_NAME)
            ->where('id', $id->toString())
            ->update(
                [
                    'is_consumed' => true,
                    'updated_at' => Carbon::now(),
                ]
            );

        if ($query === 0) {
            throw new CantUpdateException(
                sprintf(
                    self::CANT_UPDATE_EXCEPTION,
                    $id->toString()
                )
            );
        }
    }
}
