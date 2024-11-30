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
        $strLength = 256;
        Schema::create('categories', function (Blueprint $table) use ($strLength) {
            $table->id();
            $table->string('name', $strLength);
            $table->string('slug', $strLength * 2)->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('post_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id')->index();
            $table->unsignedBigInteger('category_id')->index();
            $table->timestamps();

            $table->foreign('post_id')->references('id')->on('posts')->cascadeOnDelete()->constrained();
            $table->foreign('category_id')->references('id')->on('categories')->cascadeOnDelete()->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_categories');
        Schema::dropIfExists('categories');
    }
};
