<?php

declare(strict_types=1);

return [
    'MokaPaymentDetailList.InvalidRequest'                          => 'PaymentId veya OtherTrxCode parametrelerinden en az biri verilmelidir',
    'PaymentDealer.CheckPaymentDealerAuthentication.InvalidRequest' => 'Geçersiz istek. CheckKey hatalı ya da nesne hatalı ya da JSON bozuk olabilir.',
    'PaymentDealer.CheckPaymentDealerAuthentication.InvalidAccount' => 'Geçersiz bayi hesabı.',
    'PaymentDealer.GetDealerPaymentTrxDetailList.PaymentIdError'    => 'Ödeme ID boş, 0 veya yanlış formatta gönderildi.',
    'EX'                                                            => 'Beklenmeyen bir hata oluştu.',
];
