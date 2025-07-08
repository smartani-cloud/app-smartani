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
        Schema::create('tref_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('code');
			$table->string('name');
			$table->string('category');
            $table->timestamps();
			
			$table->unique(['category', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tref_statuses');
    }
};
