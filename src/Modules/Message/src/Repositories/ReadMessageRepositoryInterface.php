<?php

declare(strict_types=1);

namespace BotHelp\WebApi\Modules\Message\Repositories;

use BotHelp\WebApi\Modules\Message\Dto\MessageDto;
use BotHelp\WebApi\Modules\Message\Exceptions\CantFindException;

interface ReadMessageRepositoryInterface
{
    /**
     * @throws CantFindException
     */
    public function getFirstNotConsumedMessageByAccountId(int $accountId): MessageDto;
}
