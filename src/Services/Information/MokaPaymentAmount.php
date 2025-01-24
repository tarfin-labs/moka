<?php

namespace Tarfin\Moka\Services\Information;

use Tarfin\Moka\Exceptions\MokaException;
use Tarfin\Moka\MokaRequest;

class MokaPaymentAmount extends MokaRequest
{
    private const ENDPOINT_CALC_PAYMENT_AMOUNT_INQUIRY = '/PaymentDealer/DoCalcPaymentAmount';

    /**
     * @throws \Tarfin\Moka\Exceptions\MokaException
     */
    public function calculate(
        string $binNumber,
        float $amount,
        int $installment = 1,
        int $isThreeD = 1,
        ?string $currency = null
    ): array {
        $requestData = [
            'PaymentDealerRequest' => [
                'BinNumber' => $binNumber,
                'Currency' => $currency ?? config('moka.currency'),
                'OrderAmount' => $amount,
                'InstallmentNumber' => $installment,
                'GroupRevenueRate' => 0,
                'GroupRevenueAmount' => 0,
                'IsThreeD' => $isThreeD,
            ],
        ];

        $response = $this->sendRequest(self::ENDPOINT_CALC_PAYMENT_AMOUNT_INQUIRY, $requestData);

        if ($response['ResultCode'] !== 'Success') {
            throw new MokaException(
                $response['ResultMessage'],
                $response['ResultCode']
            );
        }

        return $response['Data'];
    }
}
