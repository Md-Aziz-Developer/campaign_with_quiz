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
        Schema::create('response_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('response_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->text('answer_text')->nullable();
            $table->decimal('answer_number', 15, 4)->nullable();
            $table->json('selected_option_ids')->nullable(); // array of option ids for mcq_multi
            $table->decimal('score', 8, 2)->default(0);
            $table->timestamps();
            $table->index('response_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('response_answers');
    }
};
