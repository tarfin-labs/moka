<?php

declare(strict_types=1);

namespace Tarfin\Moka\Services\Information;

use Tarfin\Moka\MokaRequest;
use Tarfin\Moka\Exceptions\MokaPaymentTableException;

class MokaPaymentTable extends MokaRequest
{
    private const ENDPOINT_CALC_PAYMENT_TABLE = '/PaymentDealer/DoCalcPaymentTable';

    public function calculate(
        float $amount,
        string $binNumber = '',
        int $isThreeD = 1,
        int $isIncludedCommissionAmount = 1,
        ?string $currency = null
    ): array {
        $requestData = [
            'PaymentDealerRequest' => [
                'BinNumber'                  => $binNumber,
                'Currency'                   => $currency ?? config('moka.currency'),
                'OrderAmount'                => $amount,
                'IsThreeD'                   => $isThreeD,
                'IsIncludedCommissionAmount' => $isIncludedCommissionAmount,
            ],
        ];

        $response = $this->sendRequest(self::ENDPOINT_CALC_PAYMENT_TABLE, $requestData);

        if ($response['ResultCode'] !== 'Success') {
            throw new MokaPaymentTableException(
                $response['ResultMessage'],
                $response['ResultCode']
            );
        }

        return $response['Data'];
    }
}
