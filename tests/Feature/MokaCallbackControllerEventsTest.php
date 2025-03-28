<?php

declare(strict_types=1);

use Tarfin\Moka\Models\MokaPayment;
use Illuminate\Support\Facades\Event;
use Tarfin\Moka\Events\MokaPaymentFailedEvent;
use Tarfin\Moka\Events\MokaPaymentSucceededEvent;

beforeEach(function (): void {
    Event::fake([
        MokaPaymentSucceededEvent::class,
        MokaPaymentFailedEvent::class,
    ]);
});

it('dispatches MokaPaymentSucceededEvent event when callback indicates success', function (): void {
    $payment = MokaPayment::factory()->create([
        'other_trx_code' => '12345',
        'code_for_hash'  => 'ABCDE',
    ]);

    $hashValue = hash('sha256', strtoupper('ABCDE').'T');

    $this->post(route('moka-callback.handle3D'), [
        'OtherTrxCode'  => '12345',
        'hashValue'     => $hashValue,
        'trxCode'       => '67890',
        'resultCode'    => '00',
        'resultMessage' => 'Success',
    ]);

    Event::assertDispatched(MokaPaymentSucceededEvent::class, static function ($event) use ($payment) {
        return $event->mokaPayment->id === $payment->id;
    });
});

it('dispatches MokaPaymentFailedEvent event when callback indicates failure', function (): void {
    $payment = MokaPayment::factory()->create([
        'other_trx_code' => '12345',
        'code_for_hash'  => 'ABCDE',
    ]);

    $this->post(route('moka-callback.handle3D'), [
        'OtherTrxCode'  => '12345',
        'hashValue'     => 'invalid_hash',
        'trxCode'       => '67890',
        'resultCode'    => '01',
        'resultMessage' => 'Failed',
    ]);

    Event::assertDispatched(MokaPaymentFailedEvent::class, static function ($event) use ($payment) {
        return $event->mokaPayment->id === $payment->id;
    });
});

it('redirects to success URL after dispatching success event', function (): void {
    $payment = MokaPayment::factory()->create([
        'other_trx_code' => '12345',
        'code_for_hash'  => 'ABCDE',
    ]);

    $hashValue = hash('sha256', strtoupper('ABCDE').'T');

    $response = $this->post(route('moka-callback.handle3D', ['success_url' => 'https://example.com/success']), [
        'OtherTrxCode'  => '12345',
        'hashValue'     => $hashValue,
        'trxCode'       => '67890',
        'resultCode'    => '00',
        'resultMessage' => 'Success',
    ]);

    Event::assertDispatched(MokaPaymentSucceededEvent::class);

    $response->assertRedirect();
    expect($response->getTargetUrl())->toContain('https://example.com/success')
        ->toContain('other_trx_code=12345');
});

it('redirects to failure URL after dispatching failure event', function (): void {
    $payment = MokaPayment::factory()->create([
        'other_trx_code' => '12345',
        'code_for_hash'  => 'ABCDE',
    ]);

    $response = $this->post(route('moka-callback.handle3D', ['failure_url' => 'https://example.com/failure']), [
        'OtherTrxCode'  => '12345',
        'hashValue'     => 'invalid_hash',
        'trxCode'       => '67890',
        'resultCode'    => '01',
        'resultMessage' => 'Failed',
    ]);

    Event::assertDispatched(MokaPaymentFailedEvent::class);

    $response->assertRedirect();
    expect($response->getTargetUrl())->toContain('https://example.com/failure')
        ->toContain('other_trx_code=12345');
});

it('falls back to config success_url when none provided', function (): void {
    config(['moka.payment_success_url' => 'https://default-success.com']);

    MokaPayment::factory()->create([
        'other_trx_code' => '12345',
        'code_for_hash'  => 'ABCDE',
    ]);

    $hashValue = hash('sha256', strtoupper('ABCDE').'T');

    $response = $this->post(route('moka-callback.handle3D'), [
        'OtherTrxCode'  => '12345',
        'hashValue'     => $hashValue,
        'trxCode'       => '67890',
        'resultCode'    => '00',
        'resultMessage' => 'Success',
    ]);

    Event::assertDispatched(MokaPaymentSucceededEvent::class);

    $response->assertRedirect();
    expect($response->getTargetUrl())->toContain('https://default-success.com')
        ->toContain('other_trx_code=12345');
});

it('falls back to config failure_url when none provided', function (): void {
    config(['moka.payment_failure_url' => 'https://default-failure.com']);

    MokaPayment::factory()->create([
        'other_trx_code' => '12345',
        'code_for_hash'  => 'ABCDE',
    ]);

    $response = $this->post(route('moka-callback.handle3D'), [
        'OtherTrxCode'  => '12345',
        'hashValue'     => 'invalid_hash',
        'trxCode'       => '67890',
        'resultCode'    => '01',
        'resultMessage' => 'Failed',
    ]);

    Event::assertDispatched(MokaPaymentFailedEvent::class);

    $response->assertRedirect();
    expect($response->getTargetUrl())->toContain('https://default-failure.com')
        ->toContain('other_trx_code=12345');
});
