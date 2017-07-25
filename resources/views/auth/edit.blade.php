@extends('layouts.default')
@section('title', '机场巴士')
@section('content')
    <div>
        @include('layouts.header',['title'=>'修改密码','leftBtnType'=>1])
        <main id="pages" class="page-wrap">
            <div class="page">
                <div class="wrap">
                    <div class="login-form pt-5 pb-5 mt-100">
                        <div class="lf-item display-flex">
                            <svg class="icon-svg icon-lock">
                                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/images/icons.svg#icon-lock"></use>
                            </svg>
                            <input type="password" class="box-flex-1" id="js_ae_src_password"  placeholder="请输入原密码" value="">
                        </div>

                        <div class="lf-item display-flex">
                            <svg class="icon-svg icon-lock">
                                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/images/icons.svg#icon-lock"></use>
                            </svg>
                            <input type="password" class="box-flex-1" id="js_ae_new_password"  placeholder="请输入新密码" value="">
                        </div>

                        <div class="lf-item display-flex">
                            <svg class="icon-svg icon-lock">
                                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/images/icons.svg#icon-lock"></use>
                            </svg>
                            <input type="password" class="box-flex-1" id="js_ae_confirm_password"  placeholder="请确认新密码" value="">
                        </div>
                    </div>

                    <button class="text-center btn-primary full-width mt-20" id="js_reset_btn">确定</button>
                </div>
            </div>
        </main>
    </div>

@stop
