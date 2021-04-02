<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid('code');
            $table->float('value')->nullable(false);

            $table->enum('status', ['pendding', 'approved', 'failed'])->default('pendding');

            $table->unsignedBigInteger('user_id')->comment('payer');
            $table->foreign('user_id')->references('id')->on('users');

            $table->unsignedBigInteger('payeer_id')->comment('payeer');
            $table->foreign('payeer_id')->references('id')->on('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
