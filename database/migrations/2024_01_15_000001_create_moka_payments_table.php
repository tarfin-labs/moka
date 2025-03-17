<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Tarfin\Moka\Enums\MokaPaymentStatus;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('moka_payments', function (Blueprint $table): void {
            $table->id();
            $table->string('other_trx_code');
            $table->string('trx_code')->nullable();
            $table->string('card_holder')->nullable();
            $table->string('card_type')->nullable();
            $table->string('card_last_four', 4)->nullable();
            $table->string('code_for_hash')->nullable();
            $table->unsignedTinyInteger('status')->default(MokaPaymentStatus::PENDING->value);
            $table->decimal('amount', 10, 2);
            $table->unsignedTinyInteger('installment')->default(1);
            $table->string('result_code')->nullable();
            $table->string('result_message')->nullable();
            $table->unsignedTinyInteger('three_d')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('moka_payments');
    }
};
