<?php

declare(strict_types=1);

use Tarfin\Moka\Facades\Moka;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tarfin\Moka\Exceptions\MokaBinInquiryException;

beforeEach(function (): void {
    config([
        'moka.dealer_code'  => 'test_dealer',
        'moka.username'     => 'test_user',
        'moka.password'     => 'test_pass',
        'moka.check_key'    => 'test_check_key',
        'moka.sandbox_mode' => true,
    ]);
});

it('can get bin inquiry successfully', function (): void {
    $mockResponse = [
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

    Http::fake([
        'service.refmoka.com/*' => Http::response($mockResponse),
    ]);

    $result = Moka::binInquiry()->get('526911');

    Http::assertSent(function (Request $request) {
        return $request->url() == 'https://service.refmoka.com/PaymentDealer/GetBankCardInformation' &&
               $request->method() === 'POST' &&
               $request['BankCardInformationRequest']['BinNumber'] === '526911';
    });

    expect($result)->toBe($mockResponse['Data']);
});

it('throws exception when bin inquiry fails', function (): void {
    $mockResponse = [
        'Data'          => null,
        'ResultCode'    => 'PaymentDealer.GetBankCardInformation.BinNumberNotFound',
        'ResultMessage' => '',
        'Exception'     => null,
    ];

    Http::fake([
        'service.refmoka.com/*' => Http::response($mockResponse),
    ]);

    expect(fn () => Moka::binInquiry()->get('123456'))
        ->toThrow(function (MokaBinInquiryException $exception): void {
            expect($exception->getMessage())->toBe(__('moka::bin-inquiry.PaymentDealer.GetBankCardInformation.BinNumberNotFound'))
                ->and($exception->getCode())->toBe('PaymentDealer.GetBankCardInformation.BinNumberNotFound');
        });

    Http::assertSent(function (Request $request) {
        return $request->url() === 'https://service.refmoka.com/PaymentDealer/GetBankCardInformation' &&
            $request->method() === 'POST' &&
            $request['BankCardInformationRequest']['BinNumber'] === '123456';
    });
});
