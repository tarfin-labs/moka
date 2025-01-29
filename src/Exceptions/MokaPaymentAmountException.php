<?php

namespace Tarfin\Moka\Exceptions;

class MokaPaymentAmountException extends MokaException
{
    public function __construct(string $message, string $code)
    {
        $translatedMessage = __('moka::payment-amount.'.$code);

        parent::__construct($translatedMessage ?: $message, $code);
    }
}
