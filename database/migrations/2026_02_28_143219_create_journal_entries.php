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
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained();
            $table->foreignId('transaction_id')->nullable()->constrained();
            $table->foreignId('created_by')->constrained('users');
            $table->string('entry_number');    // "JE-2026-02-0001"
            $table->string('description');
            $table->date('date');
            $table->enum('status', ['draft', 'posted']);
            $table->string('source')->default('manual');
            // manual, transaction, invoice, subscription
            $table->timestamps();
        });

        Schema::create('journal_entry_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_entry_id')->constrained()->cascadeOnDelete();
            $table->foreignId('account_id')->constrained('chart_of_accounts');
            $table->text('description')->nullable();
            $table->decimal('debit', 20, 2)->default(0);
            $table->decimal('credit', 20, 2)->default(0);
            $table->timestamps();
            // Aturan: total debit HARUS selalu = total kredit dalam satu jurnal
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_entries');
        Schema::dropIfExists('journal_entry_lines');
    }
};
