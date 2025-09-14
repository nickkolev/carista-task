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
        Schema::create('newsapi_responses', function (Blueprint $table) {
            $table->id();
            $table->string('keyword');
            $table->json('response');
            $table->integer('page')->default(1);
            $table->integer('page_size')->default(20);
            $table->date('from_date')->nullable();
            $table->date('to_date')->nullable();
            $table->timestamps();
            
            // Add indexes for better performance
            $table->index(['keyword', 'page', 'page_size']);
            $table->index(['keyword', 'from_date', 'to_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('newsapi_responses');
    }
};
