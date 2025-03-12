<?php

use Illuminate\Support\Facades\Event;
use Tarfin\Moka\Enums\MokaPaymentStatus;
use Tarfin\Moka\Events\MokaPaymentFailed;
use Tarfin\Moka\Events\MokaPaymentSucceeded;
use Tarfin\Moka\Models\MokaPayment;

beforeEach(function () {
    Event::fake([
        MokaPaymentSucceeded::class,
        MokaPaymentFailed::class,
    ]);
});

it('dispatches MokaPaymentSucceeded event on successful 3D callback', function () {
    $payment = MokaPayment::factory()->create([
        'other_trx_code' => 'test-transaction-123',
        'code_for_hash' => 'test-hash-code',
        'amount' => 100.00,
    ]);

    $payment->handle3DCallback(
        hashValue: hash('sha256', strtoupper($payment->code_for_hash).'T'),
        resultCode: '0000',
        resultMessage: 'Success',
        trxCode: 'ORDER-17131QQFG04026575'
    );

    Event::assertDispatched(MokaPaymentSucceeded::class, static function ($event) use ($payment) {
        return $event->mokaPayment->id === $payment->id
            && $event->mokaPayment->status === MokaPaymentStatus::SUCCESS;
    });

    Event::assertNotDispatched(MokaPaymentFailed::class);
});

it('dispatches MokaPaymentFailed event on failed 3D callback', function () {
    $payment = MokaPayment::factory()->create([
        'other_trx_code' => 'test-transaction-123',
        'code_for_hash' => 'test-hash-code',
        'amount' => 100.00,
    ]);

    $payment->handle3DCallback(
        hashValue: hash('sha256', strtoupper($payment->code_for_hash).'F'),
        resultCode: '0001',
        resultMessage: 'Failed',
        trxCode: 'ORDER-17131QQFG04026575'
    );
    Event::assertDispatched(MokaPaymentFailed::class, static function ($event) use ($payment) {
        return $event->mokaPayment->id === $payment->id
            && $event->mokaPayment->status === MokaPaymentStatus::FAILED;
    });

    Event::assertNotDispatched(MokaPaymentSucceeded::class);
});

it('dispatches MokaPaymentFailed event when hash validation fails', function () {
    $payment = MokaPayment::factory()->create([
        'other_trx_code' => 'test-transaction-123',
        'code_for_hash' => 'test-hash-code',
        'amount' => 100.00,
    ]);

    $invalidHashValue = 'invalid-hash-value';

    $payment->handle3DCallback(
        hashValue: $invalidHashValue,
        resultCode: '0001',
        resultMessage: 'Invalid hash',
        trxCode: 'ORDER-17131QQFG04026575'
    );

    Event::assertDispatched(MokaPaymentFailed::class, static function ($event) use ($payment) {
        return $event->mokaPayment->id === $payment->id
            && $event->mokaPayment->status === MokaPaymentStatus::FAILED;
    });

    Event::assertNotDispatched(MokaPaymentSucceeded::class);
});
