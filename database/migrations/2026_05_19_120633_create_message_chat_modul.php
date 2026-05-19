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
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            // multi tenant
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            // private | group
            $table->enum('type', ['private', 'group'])->default('private');
            // unique key untuk private chat
            // contoh:
            // 2_8
            $table->string('private_key')->nullable()->index();
            // optional group name
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            // creator room
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            // cache preview latest message
            $table->foreignId('last_message_id')->nullable();
            // sorting conversation list
            $table->timestamp('last_message_at')->nullable()->index();
            // archive room
            $table->timestamp('archived_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('conversation_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // role participant
            // $table->enum('role', ['member', 'admin'])->default('member');
            // trigger unseen chat
            $table->timestamp('last_read_at')->nullable()->index();
            // online tracking
            $table->timestamp('last_seen_at')->nullable();
            // mute notification
            $table->boolean('is_muted')->default(false);
            // pin conversation
            $table->boolean('is_pinned')->default(false);
            // hide room
            $table->boolean('is_hidden')->default(false);
            // left group
            $table->timestamp('left_at')->nullable();
            $table->timestamps();
            // prevent duplicate participant
            $table->unique(['conversation_id', 'user_id']);
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
        Schema::dropIfExists('conversation_participants');
    }
};
