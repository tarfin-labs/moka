<?php

use Illuminate\Support\Facades\Http;
use Tarfin\Moka\Exceptions\MokaPaymentAmountException;
use Tarfin\Moka\Services\Information\MokaPaymentAmount;

beforeEach(function () {
    config([
        'moka.dealer_code' => 'test_dealer',
        'moka.username' => 'test_user',
        'moka.password' => 'test_pass',
        'moka.check_key' => 'test_check_key',
        'moka.sandbox_mode' => true,
        'moka.currency' => 'TL',
    ]);
});

it('can calculate payment amount with minimal parameters', function () {
    Http::fake([
        'https://service.refmoka.com/PaymentDealer/DoCalcPaymentAmount' => Http::response([
            'ResultCode' => 'Success',
            'ResultMessage' => '',
            'Exception' => null,
            'Data' => [
                'PaymentAmount' => 101.56,
                'DealerDepositAmount' => 95.0,
                'DealerCommissionRate' => 6.46,
                'DealerCommissionAmount' => 6.56,
                'DealerCommissionFixedAmount' => 0.0,
                'DealerGroupCommissionRate' => 1.54,
                'DealerGroupCommissionAmount' => 1.56,
                'DealerGroupCommissionFixedAmount' => 0.0,
                'GroupRevenueRate' => 5.0,
                'GroupRevenueAmount' => 5.0,
                'BankCard' => [
                    'BankName' => 'FINANSBANK',
                    'BankCode' => '111',
                    'BinNumber' => '526911',
                    'CardName' => '',
                    'CardType' => 'MASTER',
                    'CreditType' => 'CreditCard',
                    'CardLogo' => '',
                    'CardTemplate' => '',
                    'ProductCategory' => 'Bireysel',
                    'GroupName' => '',
                ],
            ],
        ]),
    ]);

    $paymentAmount = app(MokaPaymentAmount::class);

    $result = $paymentAmount->calculate(
        binNumber: '526911',
        amount: 100.00
    );

    Http::assertSent(function ($request) {
        return $request['PaymentDealerRequest']['BinNumber'] === '526911'
            && $request['PaymentDealerRequest']['Currency'] === 'TL'
            && $request['PaymentDealerRequest']['OrderAmount'] === 100.00
            && $request['PaymentDealerRequest']['InstallmentNumber'] === 1
            && $request['PaymentDealerRequest']['IsThreeD'] === 1;
    });

    expect($result)
        ->toBeArray()
        ->toHaveKeys([
            'PaymentAmount',
            'DealerDepositAmount',
            'DealerCommissionRate',
            'DealerCommissionAmount',
            'DealerCommissionFixedAmount',
            'DealerGroupCommissionRate',
            'DealerGroupCommissionAmount',
            'DealerGroupCommissionFixedAmount',
            'GroupRevenueRate',
            'GroupRevenueAmount',
            'BankCard',
        ])
        ->and($result['PaymentAmount'])->toBe(101.56)
        ->and($result['DealerDepositAmount'])->toBe(95)
        ->and($result['DealerCommissionRate'])->toBe(6.46)
        ->and($result['DealerCommissionAmount'])->toBe(6.56)
        ->and($result['BankCard'])->toBeArray()
        ->and($result['BankCard']['BankName'])->toBe('FINANSBANK')
        ->and($result['BankCard']['CardType'])->toBe('MASTER');
});

it('can calculate payment amount with all parameters', function () {
    Http::fake([
        'https://service.refmoka.com/PaymentDealer/DoCalcPaymentAmount' => Http::response([
            'ResultCode' => 'Success',
            'ResultMessage' => '',
            'Exception' => null,
            'Data' => [
                'PaymentAmount' => 101.56,
                'DealerDepositAmount' => 95.0,
                'DealerCommissionRate' => 6.46,
                'DealerCommissionAmount' => 6.56,
                'DealerCommissionFixedAmount' => 0.0,
                'DealerGroupCommissionRate' => 1.54,
                'DealerGroupCommissionAmount' => 1.56,
                'DealerGroupCommissionFixedAmount' => 0.0,
                'GroupRevenueRate' => 5.0,
                'GroupRevenueAmount' => 5.0,
                'BankCard' => [
                    'BankName' => 'FINANSBANK',
                    'BankCode' => '111',
                    'BinNumber' => '526911',
                    'CardName' => '',
                    'CardType' => 'MASTER',
                    'CreditType' => 'CreditCard',
                    'CardLogo' => '',
                    'CardTemplate' => '',
                    'ProductCategory' => 'Bireysel',
                    'GroupName' => '',
                ],
            ],
        ]),
    ]);

    $paymentAmount = app(MokaPaymentAmount::class);

    $result = $paymentAmount->calculate(
        binNumber: '526911',
        amount: 100.00,
        installment: 3,
        isThreeD: 0,
        currency: 'USD'
    );

    Http::assertSent(function ($request) {
        return $request['PaymentDealerRequest']['BinNumber'] === '526911'
            && $request['PaymentDealerRequest']['Currency'] === 'USD'
            && $request['PaymentDealerRequest']['OrderAmount'] === 100.00
            && $request['PaymentDealerRequest']['InstallmentNumber'] === 3
            && $request['PaymentDealerRequest']['IsThreeD'] === 0;
    });

    expect($result)
        ->toBeArray()
        ->toHaveKeys([
            'PaymentAmount',
            'DealerDepositAmount',
            'DealerCommissionRate',
            'DealerCommissionAmount',
            'DealerCommissionFixedAmount',
            'DealerGroupCommissionRate',
            'DealerGroupCommissionAmount',
            'DealerGroupCommissionFixedAmount',
            'GroupRevenueRate',
            'GroupRevenueAmount',
            'BankCard',
        ])
        ->and($result['PaymentAmount'])->toBe(101.56)
        ->and($result['DealerDepositAmount'])->toBe(95)
        ->and($result['DealerCommissionRate'])->toBe(6.46)
        ->and($result['DealerCommissionAmount'])->toBe(6.56)
        ->and($result['BankCard'])->toBeArray()
        ->and($result['BankCard']['BankName'])->toBe('FINANSBANK')
        ->and($result['BankCard']['CardType'])->toBe('MASTER');
});

it('throws exception when group revenue parameters are invalid', function () {
    Http::fake([
        'https://service.refmoka.com/PaymentDealer/DoCalcPaymentAmount' => Http::response([
            'ResultCode' => 'PaymentDealer.DoCalcPaymentAmount.BothGroupRevenueRateAndGroupRevenueAmountMustBeZero',
            'ResultMessage' => '',
            'Data' => null,
            'Exception' => null,
        ]),
    ]);

    $paymentAmount = app(MokaPaymentAmount::class);

    expect(fn () => $paymentAmount->calculate(
        binNumber: '555555',
        amount: 100.00
    ))->toThrow(function (MokaPaymentAmountException $exception) {
        expect($exception->getMessage())->toBe(__('moka::payment-amount.PaymentDealer.DoCalcPaymentAmount.BothGroupRevenueRateAndGroupRevenueAmountMustBeZero'))
            ->and($exception->getCode())->toBe('PaymentDealer.DoCalcPaymentAmount.BothGroupRevenueRateAndGroupRevenueAmountMustBeZero');
    });
});
