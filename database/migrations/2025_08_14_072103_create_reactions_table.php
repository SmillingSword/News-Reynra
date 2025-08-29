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
        Schema::create('reactions', function (Blueprint $table) {
            $table->id();
            $table->morphs('reactable'); // Can react to articles, comments, etc.
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('user_ip')->nullable(); // For guest reactions
            $table->enum('type', ['like', 'love', 'laugh', 'angry', 'sad', 'wow'])->default('like');
            $table->timestamps();
            
            $table->unique(['reactable_type', 'reactable_id', 'user_id', 'type'], 'unique_user_reaction');
            $table->unique(['reactable_type', 'reactable_id', 'user_ip', 'type'], 'unique_ip_reaction');
            $table->index(['reactable_type', 'reactable_id', 'type']);
            $table->index(['user_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reactions');
    }
};
