<?php

namespace Tarfin\Moka\Services\Information;

use Tarfin\Moka\Exceptions\MokaException;
use Tarfin\Moka\MokaRequest;

class MokaBinInquiry extends MokaRequest
{
    private const ENDPOINT_BIN_INQUIRY = '/PaymentDealer/GetBankCardInformation';

    public function get(string $binNumber): array
    {
        $requestData = [
            'BankCardInformationRequest' => [
                'BinNumber' => $binNumber,
            ],
        ];

        $response = $this->sendRequest(self::ENDPOINT_BIN_INQUIRY, $requestData);

        if ($response['ResultCode'] !== 'Success') {
            throw new MokaException(
                $response['ResultMessage'],
                $response['ResultCode']
            );
        }

        return $response['Data'];
    }
}
