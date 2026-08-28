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
        // 1. Clients Table (Perusahaan / Klien B2B)
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('industry')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->text('address')->nullable();
            $table->string('status')->default('prospect'); // 'lead', 'prospect', 'active', 'inactive'
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 2. Contacts Table (PIC Kontak per Perusahaan)
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->string('name');
            $table->string('title')->nullable(); // e.g. CTO, Procurement Lead, PM
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 3. Deals Table (Pipeline Peluang Proyek)
        Schema::create('deals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->string('title');
            $table->string('stage')->default('discovery'); // 'discovery', 'scoping', 'proposal_sent', 'negotiation', 'won', 'lost'
            $table->decimal('expected_value', 15, 2)->default(0);
            $table->integer('probability')->default(20);
            $table->date('expected_close_date')->nullable();
            $table->string('lost_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 4. Deal Activities Table (Log Interaksi, Meeting, WhatsApp, Call)
        Schema::create('deal_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deal_id')->nullable()->constrained('deals')->cascadeOnDelete();
            $table->foreignId('client_id')->nullable()->constrained('clients')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type')->default('note'); // 'meeting', 'call', 'whatsapp', 'email', 'note'
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('performed_at')->nullable();
            $table->timestamps();
        });

        // 5. Modifikasi projects table (Relasi ke clients & deals)
        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('client_id')->nullable()->after('user_id')->constrained('clients')->nullOnDelete();
            $table->foreignId('deal_id')->nullable()->after('client_id')->constrained('deals')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropForeign(['deal_id']);
            $table->dropColumn(['client_id', 'deal_id']);
        });

        Schema::dropIfExists('deal_activities');
        Schema::dropIfExists('deals');
        Schema::dropIfExists('contacts');
        Schema::dropIfExists('clients');
    }
};
