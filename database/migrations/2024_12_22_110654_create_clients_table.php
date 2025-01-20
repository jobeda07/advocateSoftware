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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('clientId');
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('fathers_name');
            $table->string('alternative_phone');
            $table->string('profession');
            $table->string('division_id');
            $table->string('district_id');
            $table->string('thana_id');
            $table->string('address');
            $table->text('reference')->nullable();
            $table->bigInteger('created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
