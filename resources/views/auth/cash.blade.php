@extends('layouts.default')
@section('title', '账户余额')
@section('content')
    <main id="page" >
        <div class="me-a-header">
            <p class="title">￥ <span>{{ isset($profile['balance']) ? $profile['balance']: '0' }}</span> </p>
            <p class="sub-title">{{ \App\Models\Enums\SettingEnum::transform(\App\Models\Enums\SettingEnum::App_Name_Simple) }}币</p>
        </div>
        <div id="js_drop_load_area">
            <div class="media-list media-list--right mt-10 " id="list" >

            </div>
            <button class="text-center btn-primary full-width mt-10 loading-more gone" ></button>
        </div>
    </main>

@stop
