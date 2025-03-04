<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Tarfin\Moka\Http\Controllers\MokaCallbackController;
use Tarfin\Moka\Models\MokaPayment;

it('redirects to success URL with correct parameters when payment is successful', function () {
    $payment = MokaPayment::factory()->create([
        'other_trx_code' => '12345',
        'code_for_hash' => 'ABCDE',
    ]);

    $request = Request::create('/callback', 'POST', [
        'OtherTrxCode' => '12345',
        'hashValue' => hash('sha256', strtoupper('ABCDE').'T'),
        'trxCode' => '67890',
        'resultCode' => '00',
        'resultMessage' => 'Success',
    ]);

    $controller = new MokaCallbackController;
    $response = $controller->handle3D($request);

    expect($response->getTargetUrl())->toBe(url(config('moka.payment_success_url')));
    expect(session('other_trx_code'))->toBe('12345');
    expect(session('status'))->toBe('success');
    expect(session('message'))->toBe('Success');
});

it('redirects to failed URL with correct parameters when payment fails', function () {
    $payment = MokaPayment::factory()->create([
        'other_trx_code' => '12345',
        'code_for_hash' => 'ABCDE',
    ]);

    $request = Request::create('/callback', 'POST', [
        'OtherTrxCode' => '12345',
        'hashValue' => 'invalid_hash',
        'trxCode' => '67890',
        'resultCode' => '01',
        'resultMessage' => 'Failed',
    ]);

    $controller = new MokaCallbackController;
    $response = $controller->handle3D($request);

    expect($response->getTargetUrl())->toBe(url(config('moka.payment_failed_url')));
    expect(session('other_trx_code'))->toBe('12345');
    expect(session('status'))->toBe('failed');
    expect(session('message'))->toBe('Failed');
});

it('throws ModelNotFoundException when payment is not found', function () {
    $request = Request::create('/callback', 'POST', [
        'OtherTrxCode' => 'non_existent_code',
    ]);

    $controller = new MokaCallbackController;

    expect(fn () => $controller->handle3D($request))->toThrow(ModelNotFoundException::class);
});
