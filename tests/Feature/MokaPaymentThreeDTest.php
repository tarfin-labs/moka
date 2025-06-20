<?php

declare(strict_types=1);

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\RedirectResponse;
use Tarfin\Moka\Enums\MokaPaymentStatus;
use Tarfin\Moka\Services\Payment\MokaPaymentThreeD;
use Tarfin\Moka\Exceptions\MokaPaymentThreeDException;

beforeEach(function (): void {
    config([
        'moka.dealer_code'  => 'test_dealer',
        'moka.username'     => 'test_user',
        'moka.password'     => 'test_pass',
        'moka.check_key'    => 'test_check_key',
        'moka.sandbox_mode' => true,
    ]);

    $this->mockCardInformation = [
        'ResultCode'    => 'Success',
        'ResultMessage' => '',
        'Data'          => [
            'BankName'        => 'FİNANSBANK',
            'BankCode'        => '111',
            'BinNumber'       => '526911',
            'CardName'        => '',
            'CardType'        => 'MASTER',
            'CreditType'      => 'CreditCard',
            'CardLogo'        => 'https://cdn.moka.com/Content/BankLogo/CARDFINANS.png',
            'CardTemplate'    => 'https://cdn.moka.com/Content/BankCardTemplate/FINANS-MASTER-CREDIT.png',
            'ProductCategory' => 'Bireysel',
            'GroupName'       => 'CARDFINANS',
        ],
        'Exception' => null,
    ];

    $this->mockPaymentAmount = [
        'Data' => [
            'PaymentAmount'                    => 102.04,
            'DealerDepositAmount'              => 100.0,
            'DealerCommissionRate'             => 2.0,
            'DealerCommissionAmount'           => 2.04,
            'DealerCommissionFixedAmount'      => 0.0,
            'DealerGroupCommissionRate'        => 2.0,
            'DealerGroupCommissionAmount'      => 2.04,
            'DealerGroupCommissionFixedAmount' => 0.0,
            'GroupRevenueRate'                 => 0.0,
            'GroupRevenueAmount'               => 0.0,
            'BankCard'                         => [
                'BankId'               => 4,
                'BankName'             => 'AKBANK',
                'BankCode'             => '46',
                'BinNumber'            => '512754',
                'CardName'             => 'Wings Basic MC ',
                'CardType'             => 'MASTER',
                'CreditType'           => 'CreditCard',
                'CardLogo'             => 'https://cdn.moka.com/Content/BankLogo/AXESS.png',
                'CardTemplate'         => 'https://cdn.moka.com/Content/BankCardTemplate/AKBANK-MASTER-CREDIT.png',
                'ProductCategory'      => 'Bireysel',
                'GroupName'            => 'AXESS',
                'MaxInstallmentNumber' => 0,
                'BinCountry'           => null,
            ],
        ],
        'ResultCode'    => 'Success',
        'ResultMessage' => '',
        'Exception'     => null,
    ];
});

it('can create a 3D secure payment request with all parameters', function (): void {
    Http::fake([
        'service.refmoka.com/PaymentDealer/DoDirectPaymentThreeD' => Http::response([
            'ResultCode'    => 'Success',
            'ResultMessage' => '',
            'Exception'     => null,
            'Data'          => [
                'Url'         => 'https://3d-secure-page.com',
                'CodeForHash' => 'test-hash-code',
            ],
        ]),
        'service.refmoka.com/PaymentDealer/GetBankCardInformation' => Http::response($this->mockCardInformation),
        'service.refmoka.com/PaymentDealer/DoCalcPaymentAmount'    => Http::response($this->mockPaymentAmount),
    ]);

    $payment      = app(MokaPaymentThreeD::class);
    $otherTrxCode = 'test-transaction-123';

    $result = $payment->create(
        amount: 100.00,
        cardHolderName: 'John Doe',
        cardNumber: '5555555555555555',
        expMonth: '12',
        expYear: '2025',
        cvc: '123',
        software: 'Tarfin',
        returnUrl: 'https://your-site.com/moka-callback',
        installment: 3,
        otherTrxCode: $otherTrxCode,
        isPoolPayment: 1,
        isTokenized: 1,
        currency: 'USD',
        redirectType: 2,
        language: 'EN',
        description: 'Test Payment Transaction'
    );

    Http::assertSent(static function ($request) use ($otherTrxCode) {
        return $request->url() === 'https://service.refmoka.com/PaymentDealer/DoDirectPaymentThreeD'
            && $request['PaymentDealerAuthentication']['DealerCode'] === 'test_dealer'
            && $request['PaymentDealerAuthentication']['Username'] === 'test_user'
            && $request['PaymentDealerAuthentication']['Password'] === 'test_pass'
            && $request['PaymentDealerRequest']['OtherTrxCode'] === $otherTrxCode
            && $request['PaymentDealerRequest']['InstallmentNumber'] === 3
            && $request['PaymentDealerRequest']['IsPoolPayment'] === 1
            && $request['PaymentDealerRequest']['IsTokenized'] === 1
            && $request['PaymentDealerRequest']['Currency'] === 'USD'
            && $request['PaymentDealerRequest']['RedirectType'] === 2
            && $request['PaymentDealerRequest']['Language'] === 'EN'
            && $request['PaymentDealerRequest']['IsPreAuth'] === 0
            && $request['PaymentDealerRequest']['Description'] === 'Test Payment Transaction'
            && $request['PaymentDealerRequest']['ReturnHash'] === 1
            && $request['PaymentDealerRequest']['CardToken'] === '';
    });

    expect($result)
        ->toBeInstanceOf(RedirectResponse::class)
        ->and($result->getTargetUrl())->toBe('https://3d-secure-page.com');
});

it('can create a 3D secure payment request with minimal parameters', function (): void {
    Http::fake([
        'service.refmoka.com/PaymentDealer/DoDirectPaymentThreeD' => Http::response([
            'ResultCode'    => 'Success',
            'ResultMessage' => '',
            'Exception'     => null,
            'Data'          => [
                'Url'         => 'https://3d-secure-page.com',
                'CodeForHash' => 'test-hash-code',
            ],
        ]),
        'service.refmoka.com/PaymentDealer/GetBankCardInformation' => Http::response($this->mockCardInformation),
        'service.refmoka.com/PaymentDealer/DoCalcPaymentAmount'    => Http::response($this->mockPaymentAmount),
    ]);

    $payment = app(MokaPaymentThreeD::class);

    $result = $payment->create(
        amount: 100.00,
        cardHolderName: 'John Doe',
        cardNumber: '5555555555555555',
        expMonth: '12',
        expYear: '2025',
        cvc: '123',
    );

    Http::assertSent(function ($request) {
        return $request->url() === 'https://service.refmoka.com/PaymentDealer/DoDirectPaymentThreeD'
            && $request['PaymentDealerRequest']['InstallmentNumber'] === 1
            && $request['PaymentDealerRequest']['Currency'] === 'TL'
            && $request['PaymentDealerRequest']['RedirectType'] === 1
            && $request['PaymentDealerRequest']['Language'] === 'TR'
            && $request['PaymentDealerRequest']['IsPoolPayment'] === 0
            && $request['PaymentDealerRequest']['IsTokenized'] === 0
            && Str::isUlid($request['PaymentDealerRequest']['OtherTrxCode'])
            && $request['PaymentDealerRequest']['IsPreAuth'] === 0
            && $request['PaymentDealerRequest']['ReturnHash'] === 1
            && $request['PaymentDealerRequest']['Description'] === ''
            && $request['PaymentDealerRequest']['CardToken'] === '';
    });

    expect($result)
        ->toBeInstanceOf(RedirectResponse::class)
        ->and($result->getTargetUrl())->toBe('https://3d-secure-page.com');
});

it('throws exception when payment creation fails', function (): void {
    Http::fake([
        'service.refmoka.com/PaymentDealer/DoDirectPaymentThreeD' => Http::response([
            'ResultCode'    => 'PaymentDealer.CheckCardInfo.InvalidCardInfo',
            'ResultMessage' => '',
            'Data'          => null,
            'Exception'     => null,
        ]),
        'service.refmoka.com/PaymentDealer/GetBankCardInformation' => Http::response($this->mockCardInformation),
        'service.refmoka.com/PaymentDealer/DoCalcPaymentAmount'    => Http::response($this->mockPaymentAmount),
    ]);

    $payment = app(MokaPaymentThreeD::class);

    expect(fn () => $payment->create(
        amount: 100.00,
        cardHolderName: 'John Doe',
        cardNumber: '5555555555555555',
        expMonth: '12',
        expYear: '2025',
        cvc: '123',
        returnUrl: 'https://your-site.com/moka-callback',
        software: 'Tarfin'
    ))->toThrow(function (MokaPaymentThreeDException $exception): void {
        expect($exception->getMessage())->toBe(__('moka::payment-three-d.PaymentDealer.CheckCardInfo.InvalidCardInfo'))
            ->and($exception->getCode())->toBe('PaymentDealer.CheckCardInfo.InvalidCardInfo');
    });
});

it('stores payment data in database when payment is successful', function (): void {
    Http::fake([
        'service.refmoka.com/PaymentDealer/DoDirectPaymentThreeD' => Http::response([
            'ResultCode'    => 'Success',
            'ResultMessage' => '',
            'Exception'     => null,
            'Data'          => [
                'Url'         => 'https://3d-secure-page.com',
                'CodeForHash' => 'test-hash-code',
            ],
        ]),
        'service.refmoka.com/PaymentDealer/GetBankCardInformation' => Http::response($this->mockCardInformation),
        'service.refmoka.com/PaymentDealer/DoCalcPaymentAmount'    => Http::response($this->mockPaymentAmount),
    ]);

    $payment      = app(MokaPaymentThreeD::class);
    $otherTrxCode = 'test-transaction-123';

    $payment->create(
        amount: 100.00,
        cardHolderName: 'John Doe',
        cardNumber: '5555555555555555',
        expMonth: '12',
        expYear: '2025',
        cvc: '123',
        returnUrl: 'https://your-site.com/moka-callback',
        installment: 3,
        software: 'Tarfin',
        otherTrxCode: $otherTrxCode
    );

    $this->assertDatabaseHas('moka_payments', [
        'other_trx_code' => $otherTrxCode,
        'code_for_hash'  => 'test-hash-code',
        'amount'         => 100.00,
        'installment'    => 3,
        'status'         => MokaPaymentStatus::PENDING->value,
        'result_code'    => 'Success',
        'result_message' => '',
        'three_d'        => 1,
        'card_holder'    => 'John Doe',
        'card_type'      => 'MASTER',
        'card_last_four' => '5555',
    ]);
});

it('stores failed payment data in database when enabled in config', function (): void {
    config(['moka.store_failed_payments' => true]);

    Http::fake([
        'service.refmoka.com/PaymentDealer/DoDirectPaymentThreeD' => Http::response([
            'ResultCode'    => 'PaymentDealer.CheckCardInfo.InvalidCardInfo',
            'ResultMessage' => '',
            'Data'          => null,
            'Exception'     => null,
        ]),
        'service.refmoka.com/PaymentDealer/GetBankCardInformation' => Http::response($this->mockCardInformation),
        'service.refmoka.com/PaymentDealer/DoCalcPaymentAmount'    => Http::response($this->mockPaymentAmount),
    ]);

    $payment      = app(MokaPaymentThreeD::class);
    $otherTrxCode = 'test-transaction-123';

    try {
        $payment->create(
            amount: 100.00,
            cardHolderName: 'John Doe',
            cardNumber: '5555555555555555',
            expMonth: '12',
            expYear: '2025',
            cvc: '123',
            software: 'Tarfin',
            returnUrl: 'https://your-site.com/moka-callback',
            otherTrxCode: $otherTrxCode
        );
    } catch (MokaPaymentThreeDException $e) {
        $this->assertDatabaseHas('moka_payments', [
            'other_trx_code' => $otherTrxCode,
            'amount'         => 100.00,
            'status'         => MokaPaymentStatus::FAILED->value,
            'result_code'    => 'PaymentDealer.CheckCardInfo.InvalidCardInfo',
            'result_message' => __('moka::payment-three-d.PaymentDealer.CheckCardInfo.InvalidCardInfo'),
            'three_d'        => 1,
            'card_holder'    => 'John Doe',
            'card_type'      => 'MASTER',
            'card_last_four' => '5555',
        ]);
    }
});

it('does not store failed payment data in database when disabled in config', function (): void {
    config(['moka.store_failed_payments' => false]);

    Http::fake([
        'service.refmoka.com/PaymentDealer/DoDirectPaymentThreeD' => Http::response([
            'ResultCode'    => 'PaymentDealer.CheckCardInfo.InvalidCardInfo',
            'ResultMessage' => '',
            'Data'          => null,
            'Exception'     => null,
        ]),
        'service.refmoka.com/PaymentDealer/GetBankCardInformation' => Http::response($this->mockCardInformation),
        'service.refmoka.com/PaymentDealer/DoCalcPaymentAmount'    => Http::response($this->mockPaymentAmount),
    ]);

    $payment      = app(MokaPaymentThreeD::class);
    $otherTrxCode = 'test-transaction-123';

    try {
        $payment->create(
            amount: 100.00,
            cardHolderName: 'John Doe',
            cardNumber: '5555555555555555',
            expMonth: '12',
            expYear: '2025',
            cvc: '123',
            software: 'Tarfin',
            returnUrl: 'https://your-site.com/moka-callback',
            otherTrxCode: $otherTrxCode
        );
    } catch (MokaPaymentThreeDException $e) {
        $this->assertDatabaseMissing('moka_payments', [
            'other_trx_code' => $otherTrxCode,
        ]);
    }
});

it('can create a 3D secure payment request with buyer information', function (): void {
    Http::fake([
        'service.refmoka.com/PaymentDealer/DoDirectPaymentThreeD' => Http::response([
            'ResultCode'    => 'Success',
            'ResultMessage' => '',
            'Exception'     => null,
            'Data'          => [
                'Url'         => 'https://3d-secure-page.com',
                'CodeForHash' => 'test-hash-code',
            ],
        ]),
        'service.refmoka.com/PaymentDealer/GetBankCardInformation' => Http::response($this->mockCardInformation),
        'service.refmoka.com/PaymentDealer/DoCalcPaymentAmount'    => Http::response($this->mockPaymentAmount),
    ]);

    $payment = app(MokaPaymentThreeD::class);

    $result = $payment
        ->buyerInformation(
            fullName: 'John Doe',
            gsmNumber: '5551234567',
            email: 'john@example.com',
            address: '123 Main St'
        )
        ->create(
            amount: 100.00,
            cardHolderName: 'John Doe',
            cardNumber: '5555555555555555',
            expMonth: '12',
            expYear: '2025',
            cvc: '123'
        );

    Http::assertSent(function ($request) {
        return $request->url() === 'https://service.refmoka.com/PaymentDealer/DoDirectPaymentThreeD'
            && $request['PaymentDealerRequest']['BuyerInformation']['BuyerFullName'] === 'John Doe'
            && $request['PaymentDealerRequest']['BuyerInformation']['BuyerGsmNumber'] === '5551234567'
            && $request['PaymentDealerRequest']['BuyerInformation']['BuyerEmail'] === 'john@example.com'
            && $request['PaymentDealerRequest']['BuyerInformation']['BuyerAddress'] === '123 Main St';
    });

    expect($result)
        ->toBeInstanceOf(RedirectResponse::class)
        ->and($result->getTargetUrl())->toBe('https://3d-secure-page.com');
});

it('can create a 3D secure payment request with partial buyer information', function (): void {
    Http::fake([
        'service.refmoka.com/PaymentDealer/DoDirectPaymentThreeD' => Http::response([
            'ResultCode'    => 'Success',
            'ResultMessage' => '',
            'Exception'     => null,
            'Data'          => [
                'Url'         => 'https://3d-secure-page.com',
                'CodeForHash' => 'test-hash-code',
            ],
        ]),
        'service.refmoka.com/PaymentDealer/GetBankCardInformation' => Http::response($this->mockCardInformation),
        'service.refmoka.com/PaymentDealer/DoCalcPaymentAmount'    => Http::response($this->mockPaymentAmount),
    ]);

    $payment = app(MokaPaymentThreeD::class);

    $result = $payment
        ->buyerInformation(
            fullName: 'John Doe',
            email: 'john@example.com'
        )
        ->create(
            amount: 100.00,
            cardHolderName: 'John Doe',
            cardNumber: '5555555555555555',
            expMonth: '12',
            expYear: '2025',
            cvc: '123'
        );

    Http::assertSent(function ($request) {
        return $request->url() === 'https://service.refmoka.com/PaymentDealer/DoDirectPaymentThreeD'
            && $request['PaymentDealerRequest']['BuyerInformation']['BuyerFullName'] === 'John Doe'
            && $request['PaymentDealerRequest']['BuyerInformation']['BuyerEmail'] === 'john@example.com'
            && $request['PaymentDealerRequest']['BuyerInformation']['BuyerGsmNumber'] === null
            && $request['PaymentDealerRequest']['BuyerInformation']['BuyerAddress'] === null;
    });

    expect($result)
        ->toBeInstanceOf(RedirectResponse::class)
        ->and($result->getTargetUrl())->toBe('https://3d-secure-page.com');
});

it('can create a 3D secure payment request without redirect away', function (): void {
    Http::fake([
        'service.refmoka.com/PaymentDealer/DoDirectPaymentThreeD' => Http::response([
            'ResultCode'    => 'Success',
            'ResultMessage' => '',
            'Exception'     => null,
            'Data'          => [
                'Url'         => 'https://3d-secure-page.com',
                'CodeForHash' => 'test-hash-code',
            ],
        ]),
        'service.refmoka.com/PaymentDealer/GetBankCardInformation' => Http::response($this->mockCardInformation),
        'service.refmoka.com/PaymentDealer/DoCalcPaymentAmount'    => Http::response($this->mockPaymentAmount),
    ]);

    $payment = app(MokaPaymentThreeD::class);

    $result = $payment->create(
        amount: 100.00,
        cardHolderName: 'John Doe',
        cardNumber: '5555555555555555',
        expMonth: '12',
        expYear: '2025',
        cvc: '123',
        redirectAway: false
    );

    expect($result)
        ->toBeArray()
        ->toHaveKeys([
            'Url',
            'CodeForHash',
        ])
        ->and($result['Url'])->toBe('https://3d-secure-page.com')
        ->and($result['CodeForHash'])->toBe('test-hash-code');
});
