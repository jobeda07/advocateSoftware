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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_no');
            $table->bigInteger('expense_category_id')->nullable();
            $table->string('caseId')->nullable();
            $table->double('amount');
            $table->string('payment_method');
            $table->bigInteger('created_by')->nullable();
            $table->string('voucher_image')->nullable();
            $table->longText('comment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
