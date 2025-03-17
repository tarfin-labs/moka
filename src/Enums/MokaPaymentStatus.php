<?php

declare(strict_types=1);

namespace Tarfin\Moka\Enums;

enum MokaPaymentStatus: int
{
    case PENDING = 0;
    case SUCCESS = 1;
    case FAILED  = 2;
}
