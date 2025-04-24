<?php

declare(strict_types=1);

return [
    'MokaPaymentDetailList.InvalidRequest'                          => 'Either PaymentId or OtherTrxCode must be provided',
    'PaymentDealer.CheckPaymentDealerAuthentication.InvalidRequest' => 'Invalid request. CheckKey is incorrect or object is invalid or JSON is malformed.',
    'PaymentDealer.CheckPaymentDealerAuthentication.InvalidAccount' => 'Invalid dealer account.',
    'PaymentDealer.GetDealerPaymentTrxDetailList.PaymentIdError'    => 'Payment ID is empty, 0, or in incorrect format.',
    'EX'                                                            => 'An unexpected error occurred.',
];
