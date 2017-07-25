<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivityRecordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_record', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('aid');
            $table->string('open_id'); //中奖微信用户
            $table->string('code', 20);//中奖码
            $table->tinyInteger('type');//抽奖类型0 班车 1快捷巴士
            $table->tinyInteger('status');//是否中奖 0未中奖 1已中奖
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
        Schema::dropIfExists('activity_record');
    }
}
