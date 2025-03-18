<?php

declare(strict_types=1);

namespace Tarfin\Moka\Services\Information;

use Tarfin\Moka\MokaRequest;
use Tarfin\Moka\Exceptions\MokaBinInquiryException;

class MokaBinInquiry extends MokaRequest
{
    private const ENDPOINT_BIN_INQUIRY = '/PaymentDealer/GetBankCardInformation';

    /**
     * @throws \Tarfin\Moka\Exceptions\MokaBinInquiryException
     */
    public function get(string $binNumber): array
    {
        $requestData = [
            'BankCardInformationRequest' => [
                'BinNumber' => $binNumber,
            ],
        ];

        $response = $this->sendRequest(self::ENDPOINT_BIN_INQUIRY, $requestData);

        if ($response['ResultCode'] !== 'Success') {
            throw new MokaBinInquiryException(
                $response['ResultMessage'],
                $response['ResultCode']
            );
        }

        return $response['Data'];
    }
}
