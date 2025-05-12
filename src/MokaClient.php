<?php

declare(strict_types=1);

namespace Tarfin\Moka;

use Tarfin\Moka\Services\Payment\MokaPaymentThreeD;
use Tarfin\Moka\Services\Information\MokaBinInquiry;
use Tarfin\Moka\Services\Information\MokaPaymentTable;
use Tarfin\Moka\Services\Information\MokaPaymentAmount;
use Tarfin\Moka\Services\Information\MokaPaymentDetailList;

class MokaClient
{
    public function threeDPayment(): MokaPaymentThreeD
    {
        return new MokaPaymentThreeD();
    }

    public function binInquiry(): MokaBinInquiry
    {
        return new MokaBinInquiry();
    }

    public function paymentAmount(): MokaPaymentAmount
    {
        return new MokaPaymentAmount();
    }

    public function paymentTable(): MokaPaymentTable
    {
        return new MokaPaymentTable();
    }

    public function paymentDetailList(): MokaPaymentDetailList
    {
        return new MokaPaymentDetailList();
    }
}
