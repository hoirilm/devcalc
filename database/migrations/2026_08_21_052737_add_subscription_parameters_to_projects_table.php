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
            $table->string('billing_type')->default('one_off')->after('status'); // 'one_off' or 'subscription'
            $table->string('billing_cycle')->nullable()->default('monthly')->after('billing_type'); // 'monthly' or 'yearly'
            $table->unsignedInteger('subscription_duration')->nullable()->default(12)->after('billing_cycle'); // in months or years (e.g. 12)
            $table->decimal('setup_fee', 15, 2)->default(0.00)->after('subscription_duration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'billing_type',
                'billing_cycle',
                'subscription_duration',
                'setup_fee',
            ]);
        });
    }
};
