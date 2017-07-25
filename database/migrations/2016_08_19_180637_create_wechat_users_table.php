<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWechatUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wechat_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('open_id')->unique();
            $table->string('name',100)->default('');
            $table->string('nickname',100);
            $table->tinyInteger('sex')->default(0);//值为1时是男性，值为2时是女性，值为0时是未知
            $table->tinyInteger('status')->default(0);//0 未关注 1关注
            $table->string('avatar')->default('');
            $table->string('province',50);
            $table->string('country',50);
            $table->string('city',50);
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
        Schema::drop('wechat_users');
    }
}
