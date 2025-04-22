<?php

declare(strict_types=1);

use Tarfin\Moka\Facades\Moka;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tarfin\Moka\Exceptions\MokaPaymentDetailListException;

beforeEach(function (): void {
    config([
        'moka.dealer_code'  => 'test_dealer',
        'moka.username'     => 'test_user',
        'moka.password'     => 'test_pass',
        'moka.check_key'    => 'test_check_key',
        'moka.sandbox_mode' => true,
    ]);
});

it('can get payment detail list successfully with paymentId', function (): void {
    $mockResponse = [
        'Data' => [
            'IsSuccessful'  => true,
            'ResultCode'    => '00',
            'ResultMessage' => '',
            'PaymentDetail' => [
                'DealerPaymentId'        => 27405,
                'OtherTrxCode'           => '',
                'CardHolderFullName'     => 'Ahmet Yılmaz',
                'CardNumberFirstSix'     => '554960',
                'CardNumberLastFour'     => '5523',
                'PaymentDate'            => '2017-02-28T14:42:17.26',
                'Amount'                 => 20.10,
                'RefAmount'              => 5.10,
                'CurrencyCode'           => 'TL',
                'InstallmentNumber'      => 0,
                'DealerCommissionAmount' => 0.50,
                'IsThreeD'               => false,
                'Description'            => 'Ödeme açıklaması',
                'PaymentStatus'          => 2,
                'TrxStatus'              => 1,
            ],
            'ListItemCount'        => 2,
            'PaymentTrxDetailList' => [
                [
                    'DealerPaymentTrxId' => 2971,
                    'DealerPaymentId'    => 27405,
                    'TrxCode'            => '26ba712e-6381-4291-8c59-702c13b30d4d',
                    'TrxDate'            => '2017-02-28T14:42:17.837',
                    'Amount'             => 20.10,
                    'TrxType'            => 2,
                    'TrxStatus'          => 1,
                    'PaymentReason'      => 1,
                    'VoidRefundReason'   => 0,
                    'VirtualPosOrderId'  => ' ORDER-17060RYOG07011948',
                    'ResultMessage'      => '',
                ],
                [
                    'DealerPaymentTrxId' => 2982,
                    'DealerPaymentId'    => 27405,
                    'TrxCode'            => '32c19f0f-4853-4a0d-bf7c-fbc687a826a7',
                    'TrxDate'            => '2017-02-28T14:44:32.26',
                    'Amount'             => 5.10,
                    'TrxType'            => 4,
                    'TrxStatus'          => 1,
                    'PaymentReason'      => 0,
                    'VoidRefundReason'   => 2,
                    'VirtualPosOrderId'  => ' ORDER-17060RYOG07011948',
                    'ResultMessage'      => '',
                ],
            ],
        ],
        'ResultCode'    => 'Success',
        'ResultMessage' => '',
        'Exception'     => null,
    ];

    Http::fake([
        'https://service.refmoka.com/PaymentDealer/GetDealerPaymentTrxDetailList' => Http::response($mockResponse),
    ]);

    $result = Moka::paymentDetailList()->getDetail('1170');

    Http::assertSent(function (Request $request) {
        return $request->url() == 'https://service.refmoka.com/PaymentDealer/GetDealerPaymentTrxDetailList' &&
            $request->method() === 'POST' &&
            $request['PaymentDealerRequest']['PaymentId'] === '1170';
    });

    expect($result)->toBe($mockResponse['Data']);
});

it('can get payment detail list successfully with otherTrxCode', function (): void {
    $mockResponse = [
        'Data' => [
            'IsSuccessful'  => true,
            'ResultCode'    => '00',
            'ResultMessage' => '',
            'PaymentDetail' => [
                'DealerPaymentId'        => 27405,
                'OtherTrxCode'           => 'ORDER123',
                'CardHolderFullName'     => 'Ahmet Yılmaz',
                'CardNumberFirstSix'     => '554960',
                'CardNumberLastFour'     => '5523',
                'PaymentDate'            => '2017-02-28T14:42:17.26',
                'Amount'                 => 20.10,
                'RefAmount'              => 5.10,
                'CurrencyCode'           => 'TL',
                'InstallmentNumber'      => 0,
                'DealerCommissionAmount' => 0.50,
                'IsThreeD'               => false,
                'Description'            => 'Ödeme açıklaması',
                'PaymentStatus'          => 2,
                'TrxStatus'              => 1,
            ],
            'ListItemCount'        => 2,
            'PaymentTrxDetailList' => [
                [
                    'DealerPaymentTrxId' => 2971,
                    'DealerPaymentId'    => 27405,
                    'TrxCode'            => '26ba712e-6381-4291-8c59-702c13b30d4d',
                    'TrxDate'            => '2017-02-28T14:42:17.837',
                    'Amount'             => 20.10,
                    'TrxType'            => 2,
                    'TrxStatus'          => 1,
                    'PaymentReason'      => 1,
                    'VoidRefundReason'   => 0,
                    'VirtualPosOrderId'  => ' ORDER-17060RYOG07011948',
                    'ResultMessage'      => '',
                ],
                [
                    'DealerPaymentTrxId' => 2982,
                    'DealerPaymentId'    => 27405,
                    'TrxCode'            => '32c19f0f-4853-4a0d-bf7c-fbc687a826a7',
                    'TrxDate'            => '2017-02-28T14:44:32.26',
                    'Amount'             => 5.10,
                    'TrxType'            => 4,
                    'TrxStatus'          => 1,
                    'PaymentReason'      => 0,
                    'VoidRefundReason'   => 2,
                    'VirtualPosOrderId'  => ' ORDER-17060RYOG07011948',
                    'ResultMessage'      => '',
                ],
            ],
        ],
        'ResultCode'    => 'Success',
        'ResultMessage' => '',
        'Exception'     => null,
    ];

    Http::fake([
        'https://service.refmoka.com/PaymentDealer/GetDealerPaymentTrxDetailList' => Http::response($mockResponse),
    ]);

    $result = Moka::paymentDetailList()->getDetail(null, 'ORDER123');

    Http::assertSent(function (Request $request) {
        return $request->url() == 'https://service.refmoka.com/PaymentDealer/GetDealerPaymentTrxDetailList' &&
            $request->method() === 'POST' &&
            $request['PaymentDealerRequest']['OtherTrxCode'] === 'ORDER123';
    });

    expect($result)->toBe($mockResponse['Data']);
});

it('throws exception when payment detail list fails', function (): void {
    $mockResponse = [
        'Data'          => null,
        'ResultCode'    => 'PaymentDealer.GetDealerPaymentTrxDetailList.InvalidRequest',
        'ResultMessage' => '',
        'Exception'     => null,
    ];

    Http::fake([
        'https://service.refmoka.com/PaymentDealer/GetDealerPaymentTrxDetailList' => Http::response($mockResponse),
    ]);

    expect(fn () => Moka::paymentDetailList()->getDetail('1170'))
        ->toThrow(function (MokaPaymentDetailListException $exception): void {
            expect($exception->getMessage())->toBe(__('moka::payment-detail-list.PaymentDealer.GetDealerPaymentTrxDetailList.InvalidRequest'))
                ->and($exception->getCode())->toBe('PaymentDealer.GetDealerPaymentTrxDetailList.InvalidRequest');
        });

    Http::assertSent(function (Request $request) {
        return $request->url() === 'https://service.refmoka.com/PaymentDealer/GetDealerPaymentTrxDetailList' &&
            $request->method() === 'POST' &&
            $request['PaymentDealerRequest']['PaymentId'] === '1170';
    });
});

it('throws exception when no parameters are provided', function (): void {
    expect(fn () => Moka::paymentDetailList()->getDetail(null, null))
        ->toThrow(function (MokaPaymentDetailListException $exception): void {
            expect($exception->getMessage())->toBe(__('moka::payment-detail-list.MokaPaymentDetailList.InvalidRequest'))
                ->and($exception->getCode())->toBe('MokaPaymentDetailList.InvalidRequest');
        });

    Http::assertNotSent(function (Request $request) {
        return $request->url() === 'https://service.refmoka.com/PaymentDealer/GetDealerPaymentTrxDetailList';
    });
});
