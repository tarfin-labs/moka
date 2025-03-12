<?php

namespace Tarfin\Moka\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Tarfin\Moka\Models\MokaPayment;

abstract class MokaPaymentEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public MokaPayment $mokaPayment,
    ) {}
}
