<?php

use Tarfin\Moka\Facades\Moka;
use Tarfin\Moka\Services\Payment\MokaPaymentThreeD;

test('getCardInfo returns correct card information', function () {
    Http::fake([
        'https://service.refmoka.com/PaymentDealer/GetBankCardInformation' => Http::response([
          'ResultCode' => 'Success',
          'ResultMessage' => '',
          'Data' => [
              'BankName' => 'FİNANSBANK',
              'BankCode' => '111',
              'BinNumber' => '555555',
              'CardName' => '',
              'CardType' => 'MASTER',
              'CreditType' => 'CreditCard',
              'CardLogo' => 'https://cdn.moka.com/Content/BankLogo/CARDFINANS.png',
              'CardTemplate' => 'https://cdn.moka.com/Content/BankCardTemplate/FINANS-MASTER-CREDIT.png',
              'ProductCategory' => 'Bireysel',
              'GroupName' => 'CARDFINANS',
          ],
          'Exception' => null,
      ]),
    ]);

    $cardNumber = '5555555555555555';
    $result = Moka::threeDPayment()->getCardInfo($cardNumber);

    expect($result)->toBe([
        'card_type' => 'MASTER',
        'card_last_four' => '5555',
    ]);
});