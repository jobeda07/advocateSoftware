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
        Schema::create('court_cases', function (Blueprint $table) {
            $table->id();  
            $table->string('caseId');
            $table->string('clientId');
            $table->bigInteger('client_type');
            $table->bigInteger('case_type');
            $table->bigInteger('case_category');
            $table->string('case_section');
            $table->bigInteger('case_stage');
            $table->bigInteger('court_id');
            $table->string('court_branch')->nullable();
            $table->double('fees');
            $table->string('branch')->nullable();
            $table->json('witnesses')->nullable();
            $table->string('opposition_name')->nullable();
            $table->string('opposition_phone')->nullable();
            $table->enum('priority', ['High','Medium','Low']);
            $table->enum('status', ['active','inactive']);
            $table->bigInteger('case_lawer_id')->nullable();
            $table->longtext('comments');
            $table->bigInteger('created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('court_cases');
    }
};
