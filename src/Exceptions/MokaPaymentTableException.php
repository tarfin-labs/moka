<?php

declare(strict_types=1);

namespace Tarfin\Moka\Exceptions;

class MokaPaymentTableException extends MokaException
{
    public function __construct(string $message, string $code)
    {
        $translatedMessage = __('moka::payment-table.'.$code);

        parent::__construct($translatedMessage ?: $message, $code);
    }
}
