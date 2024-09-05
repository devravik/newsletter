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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('subject');
            $table->string('from_name');
            $table->string('from_email');            
            $table->string('reply_to');            
            $table->string('status')->default('draft');
            $table->dateTime('sent_at')->nullable();
            $table->string('template');
            $table->text('content')->nullable();
            $table->json('contact_filters')->nullable();
            $table->json('meta')->nullable();
            $table->json('options')->nullable();
            $table->json('report')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
