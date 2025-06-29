<?php

declare(strict_types=1);

namespace Tarfin\Moka\Models;

use Illuminate\Database\Eloquent\Model;
use Tarfin\Moka\Enums\MokaPaymentStatus;
use Tarfin\Moka\Events\MokaPaymentFailedEvent;
use Tarfin\Moka\Events\MokaPaymentSucceededEvent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Tarfin\Moka\Database\Factories\MokaPaymentFactory;

class MokaPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'other_trx_code',
        'trx_code',
        'card_holder',
        'card_type',
        'card_last_four',
        'code_for_hash',
        'status',
        'amount',
        'amount_charged',
        'amount_commission',
        'installment',
        'result_code',
        'result_message',
        'three_d',
    ];
    protected $casts = [
        'status'            => MokaPaymentStatus::class,
        'amount'            => 'decimal:2',
        'amount_charged'    => 'decimal:2',
        'amount_commission' => 'decimal:2',
    ];

    protected static function newFactory(): MokaPaymentFactory
    {
        return MokaPaymentFactory::new();
    }

    public function handle3DCallback(
        string $hashValue,
        ?string $resultCode,
        ?string $resultMessage,
        ?string $trxCode
    ): self {
        $successHash = hash('sha256', strtoupper($this->code_for_hash).'T');
        $status      = $hashValue === $successHash
            ? MokaPaymentStatus::SUCCESS
            : MokaPaymentStatus::FAILED;

        $this->update([
            'trx_code'       => $trxCode,
            'status'         => $status,
            'result_code'    => $resultCode,
            'result_message' => $resultMessage,
        ]);

        $this->refresh();

        match ($status) {
            MokaPaymentStatus::SUCCESS => MokaPaymentSucceededEvent::dispatch($this),
            MokaPaymentStatus::FAILED  => MokaPaymentFailedEvent::dispatch($this),
        };

        return $this;
    }
}
