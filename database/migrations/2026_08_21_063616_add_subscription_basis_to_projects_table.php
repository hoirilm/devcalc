<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('subscription_basis')->default('modular')->after('billing_type'); // 'modular', 'per_user', 'hybrid'
            $table->unsignedInteger('user_count')->nullable()->default(0)->after('subscription_duration');
            $table->decimal('price_per_user', 15, 2)->default(0.00)->after('user_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'subscription_basis',
                'user_count',
                'price_per_user',
            ]);
        });
    }
};
