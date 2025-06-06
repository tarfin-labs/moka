<?php

declare(strict_types=1);

namespace Tarfin\Moka\Facades;

use Tarfin\Moka\MokaClient;
use Illuminate\Support\Facades\Facade;

/**
 * @mixin MokaClient
 */
class Moka extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return MokaClient::class;
    }
}
