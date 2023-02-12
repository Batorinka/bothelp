<?php

declare(strict_types=1);

namespace BotHelp\WebApi\Modules\Message\Providers;

use BotHelp\WebApi\Modules\Message\Console\Commands\ConsumeMessages;
use BotHelp\WebApi\Modules\Message\Console\Commands\GenerateMessages;
use Illuminate\Support\ServiceProvider;

class MassageServiceProvider extends ServiceProvider
{
    private const MIGRATIONS_PATH = 'database/migrations';

    public function boot(): void
    {
        $this->loadMigrations();
    }

    public function register(): void
    {
        $this->app->register(MessageDIServiceProvider::class);
        $this->commands(
            [
                GenerateMessages::class,
                ConsumeMessages::class,
            ]
        );
    }

    private function loadMigrations(): void
    {
        $this->loadMigrationsFrom($this->modulePath(self::MIGRATIONS_PATH));
    }

    private function modulePath(string $path): string
    {
        return sprintf('%s/../../%s', __DIR__, $path);
    }
}
