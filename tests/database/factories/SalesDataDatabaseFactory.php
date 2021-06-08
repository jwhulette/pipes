<?php

declare(strict_types=1);

namespace Tests\database\factories;

use Faker\Factory;
use Faker\Generator;
use Illuminate\Support\Facades\DB;

class SalesDataDatabaseFactory
{
    protected Generator $faker;

    protected string $table;

    public function __construct(string $table)
    {
        $this->faker = Factory::create();

        $this->table = $table;
    }

    public function create(int $amount): void
    {
        for ($i = 0; $i < $amount; $i++) {
            $data = $this->data();

            DB::table($this->table)->insert($data);
        }
    }

    public function data(): array
    {
        return [
            'region' => $this->faker->city,
            'country' => $this->faker->country,
            'item_type' => $this->faker->sentence,
            'sales_channel' => $this->faker->word,
            'order_priority' => $this->faker->numberBetween(1, 9),
            'order_date' => $this->faker->date,
            'order_id' => $this->faker->randomNumber,
            'ship_date' => $this->faker->date,
            'units_sold' => $this->faker->randomNumber,
            'unit_price' => $this->faker->randomFloat,
            'unit_cost' => $this->faker->randomFloat,
            'total_revenue' => $this->faker->randomFloat,
            'total_cost' => $this->faker->randomFloat,
            'total_profit' => $this->faker->randomFloat,
        ];
    }
}
