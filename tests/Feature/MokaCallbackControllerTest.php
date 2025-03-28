<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Tarfin\Moka\Models\MokaPayment;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tarfin\Moka\Http\Controllers\MokaCallbackController;

it('redirects to success URL with correct parameters when payment is successful', function (): void {
    $payment = MokaPayment::factory()->create([
        'other_trx_code' => '12345',
        'code_for_hash'  => 'ABCDE',
    ]);

    $request = Request::create('/moka-callback', 'POST', [
        'OtherTrxCode'  => '12345',
        'hashValue'     => hash('sha256', strtoupper('ABCDE').'T'),
        'trxCode'       => '67890',
        'resultCode'    => '00',
        'resultMessage' => 'Success',
    ]);

    $controller = new MokaCallbackController();
    $response   = $controller->handle3D($request);

    expect($response->getTargetUrl())->toBe(
        app(UrlGenerator::class)->query(config('moka.payment_success_url'), [
            'other_trx_code' => '12345',
        ])
    );
});

it('redirects to failed URL with correct parameters when payment fails', function (): void {
    $payment = MokaPayment::factory()->create([
        'other_trx_code' => '12345',
        'code_for_hash'  => 'ABCDE',
    ]);

    $request = Request::create('/moka-callback', 'POST', [
        'OtherTrxCode'  => '12345',
        'hashValue'     => 'invalid_hash',
        'trxCode'       => '67890',
        'resultCode'    => '01',
        'resultMessage' => 'Failed',
    ]);

    $controller = new MokaCallbackController();
    $response   = $controller->handle3D($request);

    expect($response->getTargetUrl())->toBe(
        app(UrlGenerator::class)->query(config('moka.payment_failure_url'), [
            'other_trx_code' => '12345',
        ])
    );
});

it('throws ModelNotFoundException when payment is not found', function (): void {
    $request = Request::create('/moka-callback', 'POST', [
        'OtherTrxCode' => 'non_existent_code',
    ]);

    $controller = new MokaCallbackController();

    expect(fn () => $controller->handle3D($request))->toThrow(ModelNotFoundException::class);
});
