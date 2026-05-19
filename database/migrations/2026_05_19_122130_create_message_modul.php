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
Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('conversation_id')->constrained()->cascadeOnDelete();
            // sender
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // frontend temporary UUID
            $table->uuid('client_id')->nullable()->index();
            // text message
            $table->longText('body')->nullable();
            // text | image | file | audio | system
            $table->enum('type', ['text','image','file','audio','system'])->default('text');
            // attachment
            $table->string('attachment_path')->nullable();
            $table->string('attachment_name')->nullable();
            $table->unsignedBigInteger('attachment_size')->nullable();
            $table->string('attachment_mime')->nullable();
            // reply message
            $table->foreignId('reply_to_id')->nullable()->constrained('messages')->nullOnDelete();
            // forwarded message
            $table->foreignId('forwarded_from_id')->nullable()->constrained('messages')->nullOnDelete();
            // edited message
            $table->timestamp('edited_at')->nullable();
            // soft delete local
            $table->softDeletes();
            $table->timestamps();
            // indexing
            $table->index(['conversation_id','created_at']);
            $table->index(['user_id','created_at']);
        });

        Schema::create('message_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained()->cascadeOnDelete();
            // receiver
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // waktu receiver ACK
            $table->timestamp('delivered_at')->index();
            $table->timestamps();
            // prevent duplicate delivery
            $table->unique(['message_id','user_id']);
        });

        Schema::create('message_reads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained()->cascadeOnDelete();
            // reader
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // waktu dibaca
            $table->timestamp('read_at')->index();
            $table->timestamps();
            // prevent duplicate read
            $table->unique(['message_id','user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
        Schema::dropIfExists('message_deliveries');
        Schema::dropIfExists('message_reads');
    }
};
