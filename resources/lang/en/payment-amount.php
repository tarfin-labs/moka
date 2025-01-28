<?php

return [
    'PaymentDealer.DoCalcPaymentAmount.InvalidRequest' => 'The CheckKey may be bad, or the object may be bad, or the JSON may be corrupt.',
    'PaymentDealer.DoCalcPaymentAmount.RequiredOrderAmount' => 'Cart amount required',
    'PaymentDealer.DoCalcPaymentAmount.InvalidCurrencyCode' => 'Currency required',
    'PaymentDealer.DoCalcPaymentAmount.InvalidInstallmentNumber' => 'The installment number is invalid',
    'PaymentDealer.DoCalcPaymentAmount.VirtualPosCommissionRateNotFound' => 'Virtual pos commission rate could not be found.',
    'PaymentDealer.DoCalcPaymentAmount.InstallmentNotAvailableForForeignCurrencyTransaction' => 'Installment transaction is not valid for foreign currency.',
    'PaymentDealer.DoCalcPaymentAmount.DealerDoNotBinNumberInquiryAllowed' => 'The dealer does not have permission to query Bin number.',
    'PaymentDealer.DoCalcPaymentAmount.BinNumberMustGiven' => 'The bin number must be given.',
    'PaymentDealer.DoCalcPaymentAmount.BothGroupRevenueRateAndGroupRevenueAmountMustBeZero' => 'Both the group income ratio and the group income amount must be 0.',
    'EX' => 'An unexpected error has occurred'
];