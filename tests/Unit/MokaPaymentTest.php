<?php

declare(strict_types=1);

use Tarfin\Moka\Models\MokaPayment;
use Tarfin\Moka\Enums\MokaPaymentStatus;

it('handles successful 3D callback', function (): void {
    $payment = MokaPayment::factory()->create([
        'other_trx_code' => 'test-transaction-123',
        'code_for_hash'  => 'test-hash-code',
        'amount'         => 100.00,
    ]);

    $hashValue = hash('sha256', 'TEST-HASH-CODE'.'T');

    $result = $payment->handle3DCallback(
        hashValue: $hashValue,
        resultCode: '',
        resultMessage: '',
        trxCode: 'ORDER-17131QQFG04026575'
    );

    expect($result)
        ->toBeInstanceOf(MokaPayment::class)
        ->and($result->status)->toBe(MokaPaymentStatus::SUCCESS)
        ->and($result->trx_code)->toBe('ORDER-17131QQFG04026575')
        ->and($result->result_code)->toBe('')
        ->and($result->result_message)->toBe('');
});

it('handles failed 3D callback', function (): void {
    $payment = MokaPayment::factory()->create([
        'other_trx_code' => 'test-transaction-123',
        'code_for_hash'  => 'test-hash-code',
        'amount'         => 100.00,
    ]);

    $hashValue = hash('sha256', 'TEST-HASH-CODE'.'F');

    $result = $payment->handle3DCallback(
        hashValue: $hashValue,
        resultCode: 'Failed',
        resultMessage: 'Insufficient funds',
        trxCode: 'ORDER-17131QQFG04026575'
    );

    expect($result)
        ->toBeInstanceOf(MokaPayment::class)
        ->and($result->status)->toBe(MokaPaymentStatus::FAILED)
        ->and($result->trx_code)->toBe('ORDER-17131QQFG04026575')
        ->and($result->result_code)->toBe('Failed')
        ->and($result->result_message)->toBe('Insufficient funds');
});

it('handles invalid hash as failed payment', function (): void {
    $payment = MokaPayment::factory()->create([
        'other_trx_code' => 'test-transaction-123',
        'code_for_hash'  => 'test-hash-code',
        'amount'         => 100.00,
    ]);

    $invalidHashValue = 'invalid-hash';

    $result = $payment->handle3DCallback(
        hashValue: $invalidHashValue,
        resultCode: '001',
        resultMessage: 'Kart Sahibinden 3D Onayı Alınamadı',
        trxCode: 'ORDER-17131QQFG04026575'
    );

    expect($result)
        ->toBeInstanceOf(MokaPayment::class)
        ->and($result->status)->toBe(MokaPaymentStatus::FAILED)
        ->and($result->trx_code)->toBe('ORDER-17131QQFG04026575')
        ->and($result->result_code)->toBe('001')
        ->and($result->result_message)->toBe('Kart Sahibinden 3D Onayı Alınamadı');
});
