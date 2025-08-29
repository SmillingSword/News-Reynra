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
        Schema::create('redirects', function (Blueprint $table) {
            $table->id();
            $table->string('old_url');
            $table->string('new_url');
            $table->integer('status_code')->default(301); // 301, 302, etc.
            $table->integer('hits')->default(0); // Track how many times redirect was used
            $table->boolean('is_active')->default(true);
            $table->text('reason')->nullable(); // Why the redirect was created
            $table->timestamp('last_hit_at')->nullable();
            $table->timestamps();
            
            $table->unique('old_url');
            $table->index(['is_active', 'old_url']);
            $table->index('hits');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('redirects');
    }
};
