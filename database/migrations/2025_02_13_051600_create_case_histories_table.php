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
        Schema::create('case_histories', function (Blueprint $table) {
            $table->id();
            $table->string('caseId');
            $table->dateTime('hearing_date_time');
            $table->string('activity');
            $table->string('court_decition');
            $table->string('case_history_image')->nullable();
            $table->string('case_history_pdf')->nullable();
            $table->longText('remarks');
            $table->bigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_histories');
    }
};
