<?php

declare(strict_types=1);

namespace BotHelp\WebApi\Modules\Message\Tests\Feature\Repositories;

use BotHelp\WebApi\Modules\Message\Database\Factories\MessageFactory;
use BotHelp\WebApi\Modules\Message\Exceptions\CantFindException;
use BotHelp\WebApi\Modules\Message\Exceptions\CantStoreException;
use BotHelp\WebApi\Modules\Message\Exceptions\CantUpdateException;
use BotHelp\WebApi\Modules\Message\Repositories\ReadMessageRepositoryInterface;
use BotHelp\WebApi\Modules\Message\Repositories\WriteMessageRepositoryInterface;
use BotHelp\WebApi\Modules\Message\Tests\AbstractTestCase;
use Ramsey\Uuid\Uuid;

class MessageRepositoryFeatureTest extends AbstractTestCase
{
    private WriteMessageRepositoryInterface $writeRepository;
    private ReadMessageRepositoryInterface $readRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->writeRepository = $this->app->get(WriteMessageRepositoryInterface::class);
        $this->readRepository = $this->app->get(ReadMessageRepositoryInterface::class);
    }

    /**
     * @test
     * @throws CantFindException
     */
    public function canGetFirstNotConsumedMessageByAccountId(): void
    {
        $accountId = $this->faker->randomElement(range(1, 1000));
        MessageFactory::new()->count(10)->create(
            [
                'is_consumed' => true,
                'account_id' => $accountId,
            ]
        );

        $id = $this->faker()->uuid();
        MessageFactory::new()->create(
            [
                'id' => $id,
                'account_id' => $accountId,
            ]
        );

        $actualMessage = $this->readRepository->getFirstNotConsumedMessageByAccountId($accountId);

        self::assertEquals($id, $actualMessage->getId()->toString());
    }

    /**
     * @test
     */
    public function cantFindFirstNotConsumedMessageByAccountId(): void
    {
        $accountId = $this->faker->randomElement(range(1, 1000));

        $this->expectException(CantFindException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Can\'t find not consumed message for account id %d',
                $accountId
            )
        );

        MessageFactory::new()->count(10)->create(
            [
                'is_consumed' => true,
                'account_id' => $accountId,
            ]
        );

        $this->readRepository->getFirstNotConsumedMessageByAccountId($accountId);
    }

    /**
     * @test
     * @throws CantStoreException
     */
    public function canStoreMessage(): void
    {
        $accountId = $this->faker->randomElement(range(1, 1000));

        $id = $this->writeRepository->store($accountId);

        $this->assertDatabaseHas('messages', [
            'id' => $id->toString(),
            'account_id' => $accountId,
            'is_consumed' => false,
        ]);
    }

    /**
     * @test
     * @throws CantUpdateException
     */
    public function canMarkMessageAsConsumed(): void
    {
        $id = Uuid::uuid6();
        MessageFactory::new()->create(
            [
                'id' => $id,
            ]
        );

        $this->assertDatabaseHas('messages', [
            'id' => $id->toString(),
            'is_consumed' => false,
        ]);

        $this->writeRepository->markAsConsumed($id);

        $this->assertDatabaseHas('messages', [
            'id' => $id->toString(),
            'is_consumed' => true,
        ]);
    }
}
