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
        Schema::create('case_extra_fees', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_no');
            $table->string('caseId');
            $table->bigInteger('created_by');
            $table->double('amount');
            $table->string('payment_type');
            $table->longText('comment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_extra_fees');
    }
};
