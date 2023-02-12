<?php

declare(strict_types=1);

namespace BotHelp\WebApi\Modules\Message\Database\Factories;

use BotHelp\WebApi\Modules\Message\Entities\Message;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition(): array
    {
        return [
            'id' => $this->faker->unique()->uuid(),
            'account_id' => $this->faker->randomElement(range(1, 1000)),
            'is_consumed' => false,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
