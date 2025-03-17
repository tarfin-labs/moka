<?php

declare(strict_types=1);

use Tarfin\Moka\Facades\Moka;
use Illuminate\Support\Facades\Http;
use Tarfin\Moka\Exceptions\MokaPaymentTableException;

beforeEach(function (): void {
    config([
        'moka.dealer_code'  => 'test_dealer',
        'moka.username'     => 'test_user',
        'moka.password'     => 'test_pass',
        'moka.check_key'    => 'test_check_key',
        'moka.sandbox_mode' => true,
        'moka.currency'     => 'TL',
    ]);
});

it('can calculate payment table successfully', function (): void {
    Http::fake([
        'https://service.refmoka.com/PaymentDealer/DoCalcPaymentTable' => Http::response([
            'Data' => [
                'BankPaymentInstallmentInfoList' => [
                    [
                        'BankInfoName'               => 'GENEL',
                        'PaymentInstallmentInfoList' => [
                            [
                                'CommissionType'              => 'CreditCard',
                                'InstallmentNumber'           => 1,
                                'DealerCommissionRate'        => 2.2,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 2.2,
                                'PerInstallmentAmount'        => 100,
                                'Amount'                      => 100,
                            ],
                            [
                                'CommissionType'              => 'DebitCard',
                                'InstallmentNumber'           => 1,
                                'DealerCommissionRate'        => -1,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 0,
                                'PerInstallmentAmount'        => 0,
                                'Amount'                      => 0,
                            ],
                            [
                                'CommissionType'              => 'ForeingCurrency',
                                'InstallmentNumber'           => 1,
                                'DealerCommissionRate'        => 0,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 0,
                                'PerInstallmentAmount'        => 100,
                                'Amount'                      => 100,
                            ],
                            [
                                'CommissionType'              => 'InternationalCard',
                                'InstallmentNumber'           => 1,
                                'DealerCommissionRate'        => -1,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 0,
                                'PerInstallmentAmount'        => 0,
                                'Amount'                      => 0,
                            ],
                            [
                                'CommissionType'              => 'CreditCard',
                                'InstallmentNumber'           => 2,
                                'DealerCommissionRate'        => -1,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 0,
                                'PerInstallmentAmount'        => 0,
                                'Amount'                      => 0,
                            ],
                            [
                                'CommissionType'              => 'CreditCard',
                                'InstallmentNumber'           => 3,
                                'DealerCommissionRate'        => -1,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 0,
                                'PerInstallmentAmount'        => 0,
                                'Amount'                      => 0,
                            ],
                            [
                                'CommissionType'              => 'CreditCard',
                                'InstallmentNumber'           => 4,
                                'DealerCommissionRate'        => -1,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 0,
                                'PerInstallmentAmount'        => 0,
                                'Amount'                      => 0,
                            ],
                            [
                                'CommissionType'              => 'CreditCard',
                                'InstallmentNumber'           => 5,
                                'DealerCommissionRate'        => -1,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 0,
                                'PerInstallmentAmount'        => 0,
                                'Amount'                      => 0,
                            ],
                            [
                                'CommissionType'              => 'CreditCard',
                                'InstallmentNumber'           => 6,
                                'DealerCommissionRate'        => -1,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 0,
                                'PerInstallmentAmount'        => 0,
                                'Amount'                      => 0,
                            ],
                            [
                                'CommissionType'              => 'CreditCard',
                                'InstallmentNumber'           => 7,
                                'DealerCommissionRate'        => -1,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 0,
                                'PerInstallmentAmount'        => 0,
                                'Amount'                      => 0,
                            ],
                            [
                                'CommissionType'              => 'CreditCard',
                                'InstallmentNumber'           => 8,
                                'DealerCommissionRate'        => -1,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 0,
                                'PerInstallmentAmount'        => 0,
                                'Amount'                      => 0,
                            ],
                            [
                                'CommissionType'              => 'CreditCard',
                                'InstallmentNumber'           => 9,
                                'DealerCommissionRate'        => -1,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 0,
                                'PerInstallmentAmount'        => 0,
                                'Amount'                      => 0,
                            ],
                            [
                                'CommissionType'              => 'CreditCard',
                                'InstallmentNumber'           => 10,
                                'DealerCommissionRate'        => -1,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 0,
                                'PerInstallmentAmount'        => 0,
                                'Amount'                      => 0,
                            ],
                            [
                                'CommissionType'              => 'CreditCard',
                                'InstallmentNumber'           => 11,
                                'DealerCommissionRate'        => -1,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 0,
                                'PerInstallmentAmount'        => 0,
                                'Amount'                      => 0,
                            ],
                            [
                                'CommissionType'              => 'CreditCard',
                                'InstallmentNumber'           => 12,
                                'DealerCommissionRate'        => -1,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 0,
                                'PerInstallmentAmount'        => 0,
                                'Amount'                      => 0,
                            ],
                        ],
                    ],
                    [
                        'BankInfoName'               => 'AXESS',
                        'PaymentInstallmentInfoList' => [
                            [
                                'CommissionType'              => 'CreditCard',
                                'InstallmentNumber'           => 1,
                                'DealerCommissionRate'        => 3,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 3,
                                'PerInstallmentAmount'        => 100,
                                'Amount'                      => 100,
                            ],
                            [
                                'CommissionType'              => 'DebitCard',
                                'InstallmentNumber'           => 1,
                                'DealerCommissionRate'        => 3,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 3,
                                'PerInstallmentAmount'        => 100,
                                'Amount'                      => 100,
                            ],
                            [
                                'CommissionType'              => 'ForeingCurrency',
                                'InstallmentNumber'           => 1,
                                'DealerCommissionRate'        => 3,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 3,
                                'PerInstallmentAmount'        => 100,
                                'Amount'                      => 100,
                            ],
                            [
                                'CommissionType'              => 'InternationalCard',
                                'InstallmentNumber'           => 1,
                                'DealerCommissionRate'        => 3,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 3,
                                'PerInstallmentAmount'        => 100,
                                'Amount'                      => 100,
                            ],
                            [
                                'CommissionType'              => 'CreditCard',
                                'InstallmentNumber'           => 2,
                                'DealerCommissionRate'        => 3,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 3,
                                'PerInstallmentAmount'        => 50,
                                'Amount'                      => 100,
                            ],
                            [
                                'CommissionType'              => 'CreditCard',
                                'InstallmentNumber'           => 3,
                                'DealerCommissionRate'        => 3,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 3,
                                'PerInstallmentAmount'        => 33.33,
                                'Amount'                      => 100,
                            ],
                            [
                                'CommissionType'              => 'CreditCard',
                                'InstallmentNumber'           => 4,
                                'DealerCommissionRate'        => 3,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 3,
                                'PerInstallmentAmount'        => 25,
                                'Amount'                      => 100,
                            ],
                            [
                                'CommissionType'              => 'CreditCard',
                                'InstallmentNumber'           => 5,
                                'DealerCommissionRate'        => 3,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 3,
                                'PerInstallmentAmount'        => 20,
                                'Amount'                      => 100,
                            ],
                            [
                                'CommissionType'              => 'CreditCard',
                                'InstallmentNumber'           => 6,
                                'DealerCommissionRate'        => 3,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 3,
                                'PerInstallmentAmount'        => 16.67,
                                'Amount'                      => 100,
                            ],
                            [
                                'CommissionType'              => 'CreditCard',
                                'InstallmentNumber'           => 7,
                                'DealerCommissionRate'        => 3,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 3,
                                'PerInstallmentAmount'        => 14.29,
                                'Amount'                      => 100,
                            ],
                            [
                                'CommissionType'              => 'CreditCard',
                                'InstallmentNumber'           => 8,
                                'DealerCommissionRate'        => 3,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 3,
                                'PerInstallmentAmount'        => 12.5,
                                'Amount'                      => 100,
                            ],
                            [
                                'CommissionType'              => 'CreditCard',
                                'InstallmentNumber'           => 9,
                                'DealerCommissionRate'        => 3,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 3,
                                'PerInstallmentAmount'        => 11.11,
                                'Amount'                      => 100,
                            ],
                            [
                                'CommissionType'              => 'CreditCard',
                                'InstallmentNumber'           => 10,
                                'DealerCommissionRate'        => 3,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 3,
                                'PerInstallmentAmount'        => 10,
                                'Amount'                      => 100,
                            ],
                            [
                                'CommissionType'              => 'CreditCard',
                                'InstallmentNumber'           => 11,
                                'DealerCommissionRate'        => 3,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 3,
                                'PerInstallmentAmount'        => 9.09,
                                'Amount'                      => 100,
                            ],
                            [
                                'CommissionType'              => 'CreditCard',
                                'InstallmentNumber'           => 12,
                                'DealerCommissionRate'        => 3,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 3,
                                'PerInstallmentAmount'        => 8.33,
                                'Amount'                      => 100,
                            ],
                        ],
                    ],
                ],
            ],
            'ResultCode'    => 'Success',
            'ResultMessage' => '',
            'Exception'     => null,
        ]),
    ]);

    $response = Moka::paymentTable()->calculate(
        amount: 100.00,
        binNumber: '526911',
        isThreeD: 1,
        isIncludedCommissionAmount: 0
    );

    expect($response)
        ->toBeArray()
        ->toHaveKey('BankPaymentInstallmentInfoList')
        ->and($response['BankPaymentInstallmentInfoList'])
        ->toBeArray()
        ->not->toBeEmpty();

    $firstBank = $response['BankPaymentInstallmentInfoList'][0];

    expect($firstBank)
        ->toHaveKey('BankInfoName')
        ->toHaveKey('PaymentInstallmentInfoList')
        ->and($firstBank['PaymentInstallmentInfoList'])
        ->toBeArray()
        ->not->toBeEmpty();

    $firstInstallment = $firstBank['PaymentInstallmentInfoList'][0];

    expect($firstInstallment)
        ->toHaveKey('CommissionType')
        ->toHaveKey('InstallmentNumber')
        ->toHaveKey('DealerCommissionRate')
        ->toHaveKey('DealerCommissionFixedAmount')
        ->toHaveKey('DealerCommissionAmount')
        ->toHaveKey('Amount')
        ->toHaveKey('PerInstallmentAmount');

    Http::assertSent(function ($request) {
        return $request['PaymentDealerRequest']['OrderAmount'] === 100.00
            && $request['PaymentDealerRequest']['BinNumber'] === '526911'
            && $request['PaymentDealerRequest']['IsThreeD'] === 1
            && $request['PaymentDealerRequest']['IsIncludedCommissionAmount'] === 0;
    });
});

it('can calculate payment table without bin number', function (): void {
    Http::fake([
        'https://service.refmoka.com/PaymentDealer/DoCalcPaymentTable' => Http::response([
            'Data' => [
                'BankPaymentInstallmentInfoList' => [
                    [
                        'BankInfoName'               => 'GENEL',
                        'PaymentInstallmentInfoList' => [
                            [
                                'CommissionType'              => 'CreditCard',
                                'InstallmentNumber'           => 1,
                                'DealerCommissionRate'        => 2.2,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 2.2,
                                'PerInstallmentAmount'        => 100,
                                'Amount'                      => 100,
                            ],
                            [
                                'CommissionType'              => 'DebitCard',
                                'InstallmentNumber'           => 1,
                                'DealerCommissionRate'        => -1,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 0,
                                'PerInstallmentAmount'        => 0,
                                'Amount'                      => 0,
                            ],
                            [
                                'CommissionType'              => 'ForeingCurrency',
                                'InstallmentNumber'           => 1,
                                'DealerCommissionRate'        => 0,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 0,
                                'PerInstallmentAmount'        => 100,
                                'Amount'                      => 100,
                            ],
                            [
                                'CommissionType'              => 'InternationalCard',
                                'InstallmentNumber'           => 1,
                                'DealerCommissionRate'        => -1,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 0,
                                'PerInstallmentAmount'        => 0,
                                'Amount'                      => 0,
                            ],
                            [
                                'CommissionType'              => 'CreditCard',
                                'InstallmentNumber'           => 2,
                                'DealerCommissionRate'        => -1,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 0,
                                'PerInstallmentAmount'        => 0,
                                'Amount'                      => 0,
                            ],
                            [
                                'CommissionType'              => 'CreditCard',
                                'InstallmentNumber'           => 3,
                                'DealerCommissionRate'        => -1,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 0,
                                'PerInstallmentAmount'        => 0,
                                'Amount'                      => 0,
                            ],
                            [
                                'CommissionType'              => 'CreditCard',
                                'InstallmentNumber'           => 4,
                                'DealerCommissionRate'        => -1,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 0,
                                'PerInstallmentAmount'        => 0,
                                'Amount'                      => 0,
                            ],
                            [
                                'CommissionType'              => 'CreditCard',
                                'InstallmentNumber'           => 5,
                                'DealerCommissionRate'        => -1,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 0,
                                'PerInstallmentAmount'        => 0,
                                'Amount'                      => 0,
                            ],
                            [
                                'CommissionType'              => 'CreditCard',
                                'InstallmentNumber'           => 6,
                                'DealerCommissionRate'        => -1,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 0,
                                'PerInstallmentAmount'        => 0,
                                'Amount'                      => 0,
                            ],
                            [
                                'CommissionType'              => 'CreditCard',
                                'InstallmentNumber'           => 7,
                                'DealerCommissionRate'        => -1,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 0,
                                'PerInstallmentAmount'        => 0,
                                'Amount'                      => 0,
                            ],
                            [
                                'CommissionType'              => 'CreditCard',
                                'InstallmentNumber'           => 8,
                                'DealerCommissionRate'        => -1,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 0,
                                'PerInstallmentAmount'        => 0,
                                'Amount'                      => 0,
                            ],
                            [
                                'CommissionType'              => 'CreditCard',
                                'InstallmentNumber'           => 9,
                                'DealerCommissionRate'        => -1,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 0,
                                'PerInstallmentAmount'        => 0,
                                'Amount'                      => 0,
                            ],
                            [
                                'CommissionType'              => 'CreditCard',
                                'InstallmentNumber'           => 10,
                                'DealerCommissionRate'        => -1,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 0,
                                'PerInstallmentAmount'        => 0,
                                'Amount'                      => 0,
                            ],
                            [
                                'CommissionType'              => 'CreditCard',
                                'InstallmentNumber'           => 11,
                                'DealerCommissionRate'        => -1,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 0,
                                'PerInstallmentAmount'        => 0,
                                'Amount'                      => 0,
                            ],
                            [
                                'CommissionType'              => 'CreditCard',
                                'InstallmentNumber'           => 12,
                                'DealerCommissionRate'        => -1,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 0,
                                'PerInstallmentAmount'        => 0,
                                'Amount'                      => 0,
                            ],
                        ],
                    ],
                    [
                        'BankInfoName'               => 'AXESS',
                        'PaymentInstallmentInfoList' => [
                            [
                                'CommissionType'              => 'CreditCard',
                                'InstallmentNumber'           => 1,
                                'DealerCommissionRate'        => 3,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 3,
                                'PerInstallmentAmount'        => 100,
                                'Amount'                      => 100,
                            ],
                            [
                                'CommissionType'              => 'DebitCard',
                                'InstallmentNumber'           => 1,
                                'DealerCommissionRate'        => 3,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 3,
                                'PerInstallmentAmount'        => 100,
                                'Amount'                      => 100,
                            ],
                            [
                                'CommissionType'              => 'ForeingCurrency',
                                'InstallmentNumber'           => 1,
                                'DealerCommissionRate'        => 3,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 3,
                                'PerInstallmentAmount'        => 100,
                                'Amount'                      => 100,
                            ],
                            [
                                'CommissionType'              => 'InternationalCard',
                                'InstallmentNumber'           => 1,
                                'DealerCommissionRate'        => 3,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 3,
                                'PerInstallmentAmount'        => 100,
                                'Amount'                      => 100,
                            ],
                            [
                                'CommissionType'              => 'CreditCard',
                                'InstallmentNumber'           => 2,
                                'DealerCommissionRate'        => 3,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 3,
                                'PerInstallmentAmount'        => 50,
                                'Amount'                      => 100,
                            ],
                            [
                                'CommissionType'              => 'CreditCard',
                                'InstallmentNumber'           => 3,
                                'DealerCommissionRate'        => 3,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 3,
                                'PerInstallmentAmount'        => 33.33,
                                'Amount'                      => 100,
                            ],
                            [
                                'CommissionType'              => 'CreditCard',
                                'InstallmentNumber'           => 4,
                                'DealerCommissionRate'        => 3,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 3,
                                'PerInstallmentAmount'        => 25,
                                'Amount'                      => 100,
                            ],
                            [
                                'CommissionType'              => 'CreditCard',
                                'InstallmentNumber'           => 5,
                                'DealerCommissionRate'        => 3,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 3,
                                'PerInstallmentAmount'        => 20,
                                'Amount'                      => 100,
                            ],
                            [
                                'CommissionType'              => 'CreditCard',
                                'InstallmentNumber'           => 6,
                                'DealerCommissionRate'        => 3,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 3,
                                'PerInstallmentAmount'        => 16.67,
                                'Amount'                      => 100,
                            ],
                            [
                                'CommissionType'              => 'CreditCard',
                                'InstallmentNumber'           => 7,
                                'DealerCommissionRate'        => 3,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 3,
                                'PerInstallmentAmount'        => 14.29,
                                'Amount'                      => 100,
                            ],
                            [
                                'CommissionType'              => 'CreditCard',
                                'InstallmentNumber'           => 8,
                                'DealerCommissionRate'        => 3,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 3,
                                'PerInstallmentAmount'        => 12.5,
                                'Amount'                      => 100,
                            ],
                            [
                                'CommissionType'              => 'CreditCard',
                                'InstallmentNumber'           => 9,
                                'DealerCommissionRate'        => 3,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 3,
                                'PerInstallmentAmount'        => 11.11,
                                'Amount'                      => 100,
                            ],
                            [
                                'CommissionType'              => 'CreditCard',
                                'InstallmentNumber'           => 10,
                                'DealerCommissionRate'        => 3,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 3,
                                'PerInstallmentAmount'        => 10,
                                'Amount'                      => 100,
                            ],
                            [
                                'CommissionType'              => 'CreditCard',
                                'InstallmentNumber'           => 11,
                                'DealerCommissionRate'        => 3,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 3,
                                'PerInstallmentAmount'        => 9.09,
                                'Amount'                      => 100,
                            ],
                            [
                                'CommissionType'              => 'CreditCard',
                                'InstallmentNumber'           => 12,
                                'DealerCommissionRate'        => 3,
                                'DealerCommissionFixedAmount' => 0,
                                'DealerCommissionAmount'      => 3,
                                'PerInstallmentAmount'        => 8.33,
                                'Amount'                      => 100,
                            ],
                        ],
                    ],
                ],
            ],
            'ResultCode'    => 'Success',
            'ResultMessage' => '',
            'Exception'     => null,
        ]),
    ]);

    $response = Moka::paymentTable()->calculate(
        amount: 100.00,
        isThreeD: 1,
        isIncludedCommissionAmount: 0
    );

    expect($response)
        ->toBeArray()
        ->toHaveKey('BankPaymentInstallmentInfoList')
        ->and($response['BankPaymentInstallmentInfoList'])
        ->toBeArray()
        ->not->toBeEmpty();

    Http::assertSent(function ($request) {
        return $request['PaymentDealerRequest']['OrderAmount'] === 100.00
            && $request['PaymentDealerRequest']['BinNumber'] === ''
            && $request['PaymentDealerRequest']['IsThreeD'] === 1
            && $request['PaymentDealerRequest']['IsIncludedCommissionAmount'] === 0;
    });
});

it('throws exception when authentication fails', function (): void {
    Http::fake([
        'https://service.refmoka.com/PaymentDealer/DoCalcPaymentTable' => Http::response([
            'Data'          => null,
            'ResultCode'    => 'PaymentDealer.CheckPaymentDealerAuthentication.InvalidAccount',
            'ResultMessage' => '',
            'Exception'     => null,
        ]),
    ]);

    config(['moka.dealer_code' => 'invalid_dealer_code']);

    expect(fn () => Moka::paymentTable()->calculate(
        amount: 100.00,
        binNumber: '555555'
    ))->toThrow(function (MokaPaymentTableException $exception): void {
        expect($exception->getMessage())->toBe(__('moka::payment-table.PaymentDealer.CheckPaymentDealerAuthentication.InvalidAccount'))
            ->and($exception->getCode())->toBe('PaymentDealer.CheckPaymentDealerAuthentication.InvalidAccount');
    });
});
