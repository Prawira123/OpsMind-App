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
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->decimal('quantity', 30, 2)->change();
            $table->decimal('price', 30, 2)->change();
            $table->decimal('total', 30, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->decimal('quantity', 8, 2)->change();
            $table->decimal('price', 8, 2)->change();
            $table->decimal('total', 8, 2)->change();
        });
    }
};
