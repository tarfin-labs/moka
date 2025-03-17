<?php

declare(strict_types=1);

namespace Tarfin\Moka\Exceptions;

use Exception;

class MokaException extends Exception
{
    public function __construct(string $message, string $code = '')
    {
        parent::__construct($message, 0);

        $this->code = $code;
    }
}
