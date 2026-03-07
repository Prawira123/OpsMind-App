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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();  
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->foreignId('debit_account_id')->constrained('accounts')->cascadeOnDelete();
            $table->foreignId('credit_account_id')->constrained('accounts')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->enum('type', ['expense', 'income'])->default('expense');
            $table->string('description');
            $table->decimal('amountTotal', 30, 2);
            $table->date('date');
            $table->string('reference_no')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['tenant_id', 'created_by', 'date']);
        });

        Schema::create('transaction_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions')->cascadeOnDelete();  
            $table->string('description');
            $table->decimal('amount', 30, 2);
            $table->decimal('unit_price', 30, 2);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
