<?php

declare(strict_types=1);

return [
    'PaymentDealer.DoCalcPaymentAmount.InvalidRequest'                                       => 'CheckKey hatalı ya da nesne hatalı ya da JSON bozuk olabilir.',
    'PaymentDealer.DoCalcPaymentAmount.RequiredOrderAmount'                                  => 'Sepet tutarı gerekli',
    'PaymentDealer.DoCalcPaymentAmount.InvalidCurrencyCode'                                  => 'Para birimi gerekli',
    'PaymentDealer.DoCalcPaymentAmount.InvalidInstallmentNumber'                             => 'Taksit numarası geçersiz',
    'PaymentDealer.DoCalcPaymentAmount.VirtualPosCommissionRateNotFound'                     => 'Sanal pos komisyon oranı bulunamadı.',
    'PaymentDealer.DoCalcPaymentAmount.InstallmentNotAvailableForForeignCurrencyTransaction' => 'Yabancı para birimi için taksit işlemi geçerli değil.',
    'PaymentDealer.DoCalcPaymentAmount.DealerDoNotBinNumberInquiryAllowed'                   => 'Bayinin Bin numarası sorgulama izni yok.',
    'PaymentDealer.DoCalcPaymentAmount.BinNumberNotFound'                                    => 'Bin numarası bulunamadı.',
    'PaymentDealer.DoCalcPaymentAmount.BinNumberMustGiven'                                   => 'Bin numarası verilmeli.',
    'PaymentDealer.DoCalcPaymentAmount.BothGroupRevenueRateAndGroupRevenueAmountMustBeZero'  => 'Hem grup gelir oranı hem de grup gelir tutarı 0 olmalıdır.',
    'EX'                                                                                     => 'Beklenmeyen bir hata oluştu',
];
