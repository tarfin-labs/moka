<?php

use Illuminate\Support\Facades\Event;
use Tarfin\Moka\Enums\MokaPaymentStatus;
use Tarfin\Moka\Events\MokaPaymentFailedEvent;
use Tarfin\Moka\Events\MokaPaymentSucceededEvent;
use Tarfin\Moka\Models\MokaPayment;

beforeEach(function () {
    Event::fake([
        MokaPaymentSucceededEvent::class,
        MokaPaymentFailedEvent::class,
    ]);
});

it('dispatches MokaPaymentSucceededEvent event on successful 3D callback', function () {
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

    Event::assertDispatched(MokaPaymentSucceededEvent::class, static function ($event) use ($payment) {
        return $event->mokaPayment->id === $payment->id
            && $event->mokaPayment->status === MokaPaymentStatus::SUCCESS;
    });

    Event::assertNotDispatched(MokaPaymentFailedEvent::class);
});

it('dispatches MokaPaymentFailedEvent event on failed 3D callback', function () {
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
    Event::assertDispatched(MokaPaymentFailedEvent::class, static function ($event) use ($payment) {
        return $event->mokaPayment->id === $payment->id
            && $event->mokaPayment->status === MokaPaymentStatus::FAILED;
    });

    Event::assertNotDispatched(MokaPaymentSucceededEvent::class);
});

it('dispatches MokaPaymentFailedEvent event when hash validation fails', function () {
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

    Event::assertDispatched(MokaPaymentFailedEvent::class, static function ($event) use ($payment) {
        return $event->mokaPayment->id === $payment->id
            && $event->mokaPayment->status === MokaPaymentStatus::FAILED;
    });

    Event::assertNotDispatched(MokaPaymentSucceededEvent::class);
});

it('passes the correct payment object to the event', function () {
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

    Event::assertDispatched(MokaPaymentSucceededEvent::class, static function ($event) use ($payment) {
        return $event->mokaPayment->id === $payment->id
            && $event->mokaPayment->trx_code === 'ORDER-17131QQFG04026575'
            && $event->mokaPayment->result_code === '0000'
            && $event->mokaPayment->result_message === 'Success'
            && $event->mokaPayment->status === MokaPaymentStatus::SUCCESS;
    });
});
