<?php

use Tarfin\Moka\Models\MokaPayment;
use Tarfin\Moka\Events\MokaPaymentEvent;
use Tarfin\Moka\Events\MokaPaymentFailed;
use Tarfin\Moka\Events\MokaPaymentSucceeded;

it('confirms MokaPaymentSucceeded is a subclass of MokaPaymentEvent', function () {
    expect(new MokaPaymentSucceeded(MokaPayment::factory()->make()))
        ->toBeInstanceOf(MokaPaymentEvent::class);
});

it('confirms MokaPaymentFailed is a subclass of MokaPaymentEvent', function () {
    expect(new MokaPaymentFailed(MokaPayment::factory()->make()))
        ->toBeInstanceOf(MokaPaymentEvent::class);
});
