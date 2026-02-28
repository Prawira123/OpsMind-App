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
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('account_type_id')->constrained();
            $table->foreignId('parent_id')->nullable()->constrained('chart_of_accounts');
            // parent_id untuk sub-akun, contoh:
            // Beban (5000) → Beban Operasional (5100) → Beban Gaji (5110)
            $table->string('code');        
            $table->string('name');       
            $table->text('description')->nullable();
            $table->decimal('balance', 20, 2)->default(0);
            $table->boolean('is_default')->default(false); // dari seeder
            $table->boolean('is_locked')->default(false);  // akun sistem, tidak bisa dihapus
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['tenant_id', 'code']); // kode unik per tenant
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chart_of_accounts');
    }
};
