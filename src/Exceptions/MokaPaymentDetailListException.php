<?php

declare(strict_types=1);

namespace Tarfin\Moka\Exceptions;

class MokaPaymentDetailListException extends MokaException
{
    public function __construct(string $message, string $code)
    {
        $translatedMessage = __('moka::payment-detail-list.'.$code);

        parent::__construct($translatedMessage ?: $message, $code);
    }
}
