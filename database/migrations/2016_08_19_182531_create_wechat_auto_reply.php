<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWechatAutoReply extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wechat_auto_reply', function (Blueprint $table) {
            $table->increments('id');
            $table->text('key');
            $table->text('content');
            $table->tinyInteger('type',false,true)->default(0);//0 文字消息 1图片消息  2 click 回复
            $table->string('media_id')->default(0);
            $table->tinyInteger('enable',false,true)->default(0);
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
        Schema::drop('wechat_auto_reply');
    }
}
