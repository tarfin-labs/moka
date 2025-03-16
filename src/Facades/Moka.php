<?php

declare(strict_types=1);

namespace Tarfin\Moka\Facades;

use Tarfin\Moka\MokaClient;
use Illuminate\Support\Facades\Facade;
use Tarfin\Moka\Services\Payment\MokaPaymentThreeD;
use Tarfin\Moka\Services\Information\MokaBinInquiry;

/**
 * @method static MokaPaymentThreeD threeDPayment()
 * @method static MokaBinInquiry binInquiry()
 * @method static array checkPayment(array $paymentData)
 * @method static array getPaymentList(array $paymentData)
 * @method static array cancelPayment(array $paymentData)
 *
 * @see \Tarfin\Moka\MokaClient
 */
class Moka extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return MokaClient::class;
    }
}
