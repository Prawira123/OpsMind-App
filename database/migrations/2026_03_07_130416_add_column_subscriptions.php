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
        Schema::table('subscriptions', function(Blueprint $table){
            $table->foreignId('subs_plan_id')->nullable()->constrained('subscription_plans')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function(Blueprint $table){
            $table->dropForeign(['subs_plan_id']);
            $table->dropColumn('subs_plan_id');
        });
    }
};
