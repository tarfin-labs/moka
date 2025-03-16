<?php

declare(strict_types=1);

namespace Tarfin\Moka\Events;

use Tarfin\Moka\Models\MokaPayment;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

abstract class MokaPaymentEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public MokaPayment $mokaPayment,
    ) {}
}
