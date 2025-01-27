<?php

namespace Tarfin\Moka\Exceptions;

class MokaPaymentThreeDException extends MokaException
{
    public function __construct(string $message, string $code)
    {
        $translatedMessage = __("moka::payment-three-d.{$code}");

        parent::__construct($translatedMessage ?: $message, $code);
    }
}