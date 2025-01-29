<?php

namespace Tarfin\Moka\Exceptions;

class MokaBinInquiryException extends MokaException
{
    public function __construct(string $message, string $code)
    {
        $translatedMessage = __('moka::bin-inquiry.'.$code);

        parent::__construct($translatedMessage ?: $message, $code);
    }
}
