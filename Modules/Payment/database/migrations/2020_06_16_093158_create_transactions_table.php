<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->bigIncrements('id');

            $table->uuid('transaction_id')->unique();
            $table->string('reference_id', 90)->unique()->nullable();
            $table->tinyInteger('payment_gateway_id')
                ->default(\Modules\Payment\Libs\Payment::PAYMENT_GATEWAY_COD);
            $table->tinyInteger('status')
                ->default(\Modules\Payment\Libs\Payment::PAYMENT_STATUS_PENDING);
            $table->text('metas')
                ->nullable()
                ->comment('Attributes according to payment gateways');
            $table->unsignedBigInteger('user_id');

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
