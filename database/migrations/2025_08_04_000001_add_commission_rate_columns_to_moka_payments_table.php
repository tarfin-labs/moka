<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class() extends Migration {
    public function up(): void
    {
        Schema::table('moka_payments', function (Blueprint $table): void {
            $table->decimal('commission_rate', 10, 2)->after('amount_commission');
        });
    }

    public function down(): void
    {
        Schema::table('moka_payments', function (Blueprint $table): void {
            $table->dropColumn('commission_rate');
        });
    }
};
