<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pay_info', function (Blueprint $table) {
            $table->increments('id');
            $table->string('open_id');
            $table->string('out_trade_no')->unique();//微信订单id
            $table->string('contract_id');//合约id
            $table->string('transaction_id')->default('');
            $table->string('prepay_id');//预支付订单
            $table->integer('product_id');//扫码商品id
            $table->float('fee');
            $table->string('ip');
            $table->string('trade_type');//0-jsapi 1-native
            $table->tinyInteger('order_type');//订单类型0 车票订单 1旅游订单 2 扫码订单
            $table->string('trade_state');
            $table->string('bank_type')->default('');
            $table->string('time_end')->default('');
            $table->string('return_code')->default('');
            $table->string('return_msg')->default('');
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
        Schema::dropIfExists('pay_info');
    }
}
