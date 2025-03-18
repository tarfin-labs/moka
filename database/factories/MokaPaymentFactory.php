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
        $amount = $this->faker->randomFloat(2, 1, 1000);

        return [
            'other_trx_code'    => $this->faker->unique()->numerify('##########'),
            'code_for_hash'     => $this->faker->regexify('[A-Z0-9]{5}'),
            'status'            => MokaPaymentStatus::PENDING,
            'amount'            => $amount,
            'amount_charged'    => $amount * 1.5,
            'amount_commission' => $amount * 0.5,
            'result_code'       => '',
            'result_message'    => '',
            'trx_code'          => null,
        ];
    }
}
