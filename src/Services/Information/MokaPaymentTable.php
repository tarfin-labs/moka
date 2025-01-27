<?php

namespace Tarfin\Moka\Services\Information;

use Tarfin\Moka\Exceptions\MokaException;
use Tarfin\Moka\MokaRequest;

class MokaPaymentTable extends MokaRequest
{
    private const ENDPOINT_CALC_PAYMENT_TABLE = '/PaymentDealer/DoCalcPaymentTable';

    /**
     * @throws \Tarfin\Moka\Exceptions\MokaException
     */
    public function calculate(
        float $amount,
        string $binNumber = '',
        int $isThreeD = 1,
        int $isIncludedCommissionAmount = 1,
        ?string $currency = null
    ): array {
        $requestData = [
            'PaymentDealerRequest' => [
                'BinNumber' => $binNumber,
                'Currency' => $currency ?? config('moka.currency'),
                'OrderAmount' => $amount,
                'IsThreeD' => $isThreeD,
                'IsIncludedCommissionAmount' => $isIncludedCommissionAmount,
            ],
        ];

        $response = $this->sendRequest(self::ENDPOINT_CALC_PAYMENT_TABLE, $requestData);

        if ($response['ResultCode'] !== 'Success') {
            throw new MokaException(
                $response['ResultMessage'],
                $response['ResultCode']
            );
        }

        return $response['Data'];
    }
}
