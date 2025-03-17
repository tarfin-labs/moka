<?php

declare(strict_types=1);

return [
    'PaymentDealer.DoCalcPaymentTable.InvalidRequest'               => 'The CheckKey may be bad, or the object may be bad, or the JSON may be corrupt.',
    'PaymentDealer.CheckPaymentDealerAuthentication.InvalidRequest' => 'The CheckKey may be bad, or the object may be bad, or the JSON may be corrupt.',
    'PaymentDealer.CheckPaymentDealerAuthentication.InvalidAccount' => 'No such dealer was found.',
    'PaymentDealer.DoCalcPaymentTable.RequiredOrderAmount'          => 'Cart amount required',
    'PaymentDealer.DoCalcPaymentTable.BinNumberNotFound'            => 'Bin number not found.',
    'PaymentDealer.DoCalcPaymentTable.InvalidCurrencyCode'          => 'The currency is incorrect. (Must be in the form of TL, USD, EUR)',
    'PaymentDealer.DoCalcPaymentTable.DealerCommissionRateNotFound' => 'No commission rate has been entered for this dealer for this virtual pos and installment.',
];
