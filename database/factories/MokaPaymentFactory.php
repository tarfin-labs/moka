<?php

declare(strict_types=1);

namespace Tarfin\Moka\Database\Factories;

use Tarfin\Moka\Models\MokaPayment;
use Tarfin\Moka\Enums\MokaPaymentStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class MokaPaymentFactory extends Factory
{
    protected $model = MokaPayment::class;

    public function definition(): array
    {
        return [
            'other_trx_code' => $this->faker->unique()->numerify('##########'),
            'code_for_hash'  => $this->faker->regexify('[A-Z0-9]{5}'),
            'status'         => MokaPaymentStatus::PENDING,
            'amount'         => $this->faker->randomFloat(2, 1, 1000),
            'result_code'    => '',
            'result_message' => '',
            'trx_code'       => null,
        ];
    }
}
