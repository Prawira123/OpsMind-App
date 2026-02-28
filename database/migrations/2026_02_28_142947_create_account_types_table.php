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
        Schema::create('account_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');        
            $table->string('code');        
            $table->enum('category', [
                'asset',       
                'liability',   
                'equity',      
                'revenue',     
                'expense',     
            ]);
            
            $table->enum('normal_balance', ['debit', 'credit']);
            $table->enum('report_section', [
                'balance_sheet',        
                'income_statement',    
                'cash_flow',            
            ]);
            $table->string('cash_flow_type')->nullable();
            // operating, investing, financing
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_types');
    }
};
