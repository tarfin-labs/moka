<?php

namespace Tarfin\Moka\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Tarfin\Moka\Database\Factories\MokaPaymentFactory;
use Tarfin\Moka\Enums\MokaPaymentStatus;

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
        'installment',
        'result_code',
        'result_message',
        'three_d',
    ];

    protected $casts = [
        'status' => MokaPaymentStatus::class,
        'amount' => 'decimal:2',
    ];

    protected static function newFactory(): MokaPaymentFactory
    {
        return MokaPaymentFactory::new();
    }

    public function handle3DCallback(
        string $hashValue,
        ?string $resultCode,
        ?string $resultMessage,
        string $trxCode
    ): self {
        $successHash = hash('sha256', strtoupper($this->code_for_hash).'T');
        $isSuccess = $hashValue === $successHash;

        $this->update([
            'trx_code' => $trxCode,
            'status' => $isSuccess ? MokaPaymentStatus::SUCCESS : MokaPaymentStatus::FAILED,
            'result_code' => $resultCode,
            'result_message' => $resultMessage,
        ]);

        return $this->refresh();
    }
}
