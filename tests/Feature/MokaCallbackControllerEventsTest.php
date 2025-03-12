<?php

use Illuminate\Support\Facades\Event;
use Tarfin\Moka\Events\MokaPaymentFailed;
use Tarfin\Moka\Events\MokaPaymentSucceeded;
use Tarfin\Moka\Models\MokaPayment;

beforeEach(function () {
    Event::fake([
        MokaPaymentSucceeded::class,
        MokaPaymentFailed::class,
    ]);
});

it('dispatches MokaPaymentSucceeded event when callback indicates success', function () {
    $payment = MokaPayment::factory()->create([
        'other_trx_code' => '12345',
        'code_for_hash' => 'ABCDE',
    ]);

    $hashValue = hash('sha256', strtoupper('ABCDE').'T');

    $this->post(route('moka-callback.handle3D'), [
        'OtherTrxCode' => '12345',
        'hashValue' => $hashValue,
        'trxCode' => '67890',
        'resultCode' => '00',
        'resultMessage' => 'Success',
    ]);

    Event::assertDispatched(MokaPaymentSucceeded::class, static function ($event) use ($payment) {
        return $event->mokaPayment->id === $payment->id;
    });
});

it('dispatches MokaPaymentFailed event when callback indicates failure', function () {
    $payment = MokaPayment::factory()->create([
        'other_trx_code' => '12345',
        'code_for_hash' => 'ABCDE',
    ]);

    $this->post(route('moka-callback.handle3D'), [
        'OtherTrxCode' => '12345',
        'hashValue' => 'invalid_hash',
        'trxCode' => '67890',
        'resultCode' => '01',
        'resultMessage' => 'Failed',
    ]);

    Event::assertDispatched(MokaPaymentFailed::class, function ($event) use ($payment) {
        return $event->mokaPayment->id === $payment->id;
    });
});

it('redirects to success URL after dispatching success event', function () {
    $payment = MokaPayment::factory()->create([
        'other_trx_code' => '12345',
        'code_for_hash' => 'ABCDE',
    ]);

    $hashValue = hash('sha256', strtoupper('ABCDE').'T');

    $response = $this->post(route('moka-callback.handle3D', ['success_url' => 'https://example.com/success']), [
        'OtherTrxCode' => '12345',
        'hashValue' => $hashValue,
        'trxCode' => '67890',
        'resultCode' => '00',
        'resultMessage' => 'Success',
    ]);

    Event::assertDispatched(MokaPaymentSucceeded::class);

    $response->assertRedirect();
    expect($response->getTargetUrl())->toContain('https://example.com/success')
        ->toContain('other_trx_code=12345');
});

it('redirects to failure URL after dispatching failure event', function () {
    $payment = MokaPayment::factory()->create([
        'other_trx_code' => '12345',
        'code_for_hash' => 'ABCDE',
    ]);

    $response = $this->post(route('moka-callback.handle3D', ['failure_url' => 'https://example.com/failure']), [
        'OtherTrxCode' => '12345',
        'hashValue' => 'invalid_hash',
        'trxCode' => '67890',
        'resultCode' => '01',
        'resultMessage' => 'Failed',
    ]);

    Event::assertDispatched(MokaPaymentFailed::class);

    $response->assertRedirect();
    expect($response->getTargetUrl())->toContain('https://example.com/failure')
        ->toContain('other_trx_code=12345');
});
