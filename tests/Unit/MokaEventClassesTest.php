<?php

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Tarfin\Moka\Events\MokaPaymentEvent;
use Tarfin\Moka\Events\MokaPaymentFailedEvent;
use Tarfin\Moka\Events\MokaPaymentSucceeded;
use Tarfin\Moka\Models\MokaPayment;

it('confirms MokaPaymentSucceeded is a subclass of MokaPaymentEvent', function () {
    expect(new MokaPaymentSucceeded(MokaPayment::factory()->make()))
        ->toBeInstanceOf(MokaPaymentEvent::class);
});

it('confirms MokaPaymentFailedEvent is a subclass of MokaPaymentEvent', function () {
    expect(new MokaPaymentFailedEvent(MokaPayment::factory()->make()))
        ->toBeInstanceOf(MokaPaymentEvent::class);
});

it('correctly stores the MokaPayment in the event', function () {
    $payment = MokaPayment::factory()->create([
        'other_trx_code' => 'test-transaction-123',
        'amount' => 150.75,
    ]);

    $successEvent = new MokaPaymentSucceeded($payment);
    $failedEvent = new MokaPaymentFailedEvent($payment);

    expect($successEvent->mokaPayment)->toBeInstanceOf(MokaPayment::class)
        ->and($successEvent->mokaPayment->other_trx_code)->toBe('test-transaction-123')
        ->and($successEvent->mokaPayment->amount)->toBe('150.75')
        ->and($failedEvent->mokaPayment)->toBeInstanceOf(MokaPayment::class)
        ->and($failedEvent->mokaPayment->other_trx_code)->toBe('test-transaction-123')
        ->and($failedEvent->mokaPayment->amount)->toBe('150.75');
});

it('verifies MokaPaymentEvent uses required traits', function () {
    $reflection = new ReflectionClass(MokaPaymentEvent::class);

    expect($reflection->hasMethod('dispatch'))->toBeTrue()
        ->and($reflection->getTraitNames())->toContain(Dispatchable::class)
        ->and($reflection->getTraitNames())->toContain(InteractsWithSockets::class)
        ->and($reflection->getTraitNames())->toContain(SerializesModels::class);
});
