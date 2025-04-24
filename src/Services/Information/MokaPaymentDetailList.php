<?php

declare(strict_types=1);

namespace Tarfin\Moka\Services\Information;

use Tarfin\Moka\MokaRequest;
use Tarfin\Moka\Exceptions\MokaPaymentDetailListException;

class MokaPaymentDetailList extends MokaRequest
{
    private const ENDPOINT_GET_PAYMENT_DETAIL_LIST = '/PaymentDealer/GetDealerPaymentTrxDetailList';

    /**
     * Get payment transaction detail list.
     *
     * This service provides the list of transactions for a single main payment record.
     * Returns both the main payment record and the transaction record information.
     *
     * @param  string|null  $paymentId  The ID of the payment record in the Moka United system
     * @param  string|null  $otherTrxCode  Your unique transaction code sent to Moka United during payment
     *
     * @return array Payment detail and transaction list
     *
     * @throws MokaPaymentDetailListException
     */
    public function get(?string $paymentId = null, ?string $otherTrxCode = null): array
    {
        if (empty($paymentId) && empty($otherTrxCode)) {
            throw new MokaPaymentDetailListException(
                'Either PaymentId or OtherTrxCode must be provided',
                'MokaPaymentDetailList.InvalidRequest'
            );
        }

        $requestData = [
            'PaymentDealerRequest' => array_filter([
                'PaymentId'    => $paymentId,
                'OtherTrxCode' => $otherTrxCode,
            ]),
        ];

        $response = $this->sendRequest(self::ENDPOINT_GET_PAYMENT_DETAIL_LIST, $requestData);

        if ($response['ResultCode'] !== 'Success') {
            throw new MokaPaymentDetailListException(
                $response['ResultMessage'] ?: 'An error occurred while getting payment detail list',
                $response['ResultCode']
            );
        }

        return $response['Data'];
    }
}
