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
        Schema::create('case_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('caseId');
            $table->string('title');
            $table->longText('details');
            $table->enum('priority', ['High','Medium','Low']);
            $table->string('date');
            $table->string('assign_to');
            $table->bigInteger('created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_tasks');
    }
};
