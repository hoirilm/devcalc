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
            $table->foreignId('parent_id')->nullable()->after('id')->constrained('projects')->nullOnDelete();
            $table->string('quotation_type')->default('standard')->after('parent_id'); // 'standard', 'addendum', 'revision'
            $table->unsignedInteger('addendum_number')->nullable()->after('quotation_type');
            $table->text('addendum_notes')->nullable()->after('setup_fee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn([
                'parent_id',
                'quotation_type',
                'addendum_number',
                'addendum_notes',
            ]);
        });
    }
};
