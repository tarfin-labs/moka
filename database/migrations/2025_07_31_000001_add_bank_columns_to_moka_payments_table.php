<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class() extends Migration {
    public function up(): void
    {
        Schema::table('moka_payments', function (Blueprint $table): void {
            $table->string('bank_name')->nullable()->after('card_last_four');
            $table->string('bank_group_name')->nullable()->after('bank_name');
        });
    }

    public function down(): void
    {
        Schema::table('moka_payments', function (Blueprint $table): void {
            $table->dropColumn(['bank_name', 'bank_group_name']);
        });
    }
};
