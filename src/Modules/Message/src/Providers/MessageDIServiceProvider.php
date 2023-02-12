<?php

declare(strict_types=1);

namespace BotHelp\WebApi\Modules\Message\Providers;

use BotHelp\WebApi\Modules\Message\Repositories\MessageRepository;
use BotHelp\WebApi\Modules\Message\Repositories\ReadMessageRepositoryInterface;
use BotHelp\WebApi\Modules\Message\Repositories\WriteMessageRepositoryInterface;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class MessageDIServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public array $bindings = [
        WriteMessageRepositoryInterface::class => MessageRepository::class,
        ReadMessageRepositoryInterface::class => MessageRepository::class,
    ];

    public function provides(): array
    {
        return array_keys($this->bindings);
    }
}
