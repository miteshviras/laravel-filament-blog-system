<?php

use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $strLength = 256;
        Schema::create('posts', function (Blueprint $table) use ($strLength) {
            $table->id();
            $table->string('title', $strLength * 2);
            $table->string('slug', $strLength * 3)->comment('for SEO-friendly URLs');
            $table->longText('content')->comment('long text for tutorial details');
            $table->longText('summary')->comment('short description')->nullable();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('difficulty_level')->nullable();
            $table->string('duration')->comment('e.g., "2 hours"')->nullable();
            $table->string('thumbnail', $strLength * 3)->nullable();
            $table->boolean('is_published')->default(true);
            $table->datetime('published_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete()->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
