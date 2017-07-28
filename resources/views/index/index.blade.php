@extends('layouts.default')
@section('title', '首页')

{{--内容区域--}}
@section('content')
    @include('layouts.header')
    {{--banner--}}
    <div class="container-fluid bc-banner">
        <img class="pic" src="/images/banner/banner.png" width="100%">
        <div class="container text-center bcb-content">
            <div class="row">
                <div class="col-sm-8 col-sm-offset-2">
                    <div class="col-xs-12">
                        <div class="input-mask"><span class="b-icon-search"></span>搜一搜</div>
                        <input type="text" class="form-control input-lg bcb-search" placeholder="" >
                        <div class="bcb-word">
                            热门关键字：<a href="#">工程</a><a href="#">工程</a><a href="#">工程</a><a href="#">工程</a><a href="#">工程</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{--content--}}
    <div class="container-fluid mt-20">
        <div class="container">
            <div class="row bg-white box-shadow-1">
                <div class="col-sm-8 col-xs-12 ">
                    <div class="row ">
                        <div class="bc-section-title">数据统计</div>
                        <div class="col-xs-4">
                            <p>招标信息</p>
                            <p>42</p>
                            <p>今日更新<span>12</span>条信息</p>
                        </div>
                        <div class="col-xs-4">
                            <p>中标信息</p>
                            <p>42</p>
                            <p>今日更新<span>12</span>条信息</p>
                        </div>
                        <div class="col-xs-4">
                            <p>竞争对手</p>
                            <p>42</p>
                            <p>今日更新<span>12</span>条信息</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 pl-20">
                    <div class="bg-grey-bar"></div>
                    <div class="bc-section-title orange">增值业务</div>
                    <div class="bc-side-list">
                        <div class="bcs-item">
                            <a href="#">
                                <img src="/images/banner/company_data.png">
                                <p>企业数据分析</p>
                            </a>
                        </div>
                        <div class="bcs-item">
                            <a href="#">
                                <img src="/images/banner/business_data.png">
                                <p>时长数据分析</p>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row  mt-20  bg-white box-shadow-1">
                <div class="bc-section-title purple">最新发布</div>
                <div class="col-xs-12">

                    content
                </div>
                <div class="col-xs-12 text-center">
                    <ul class="pagination">
                        <li ><a href="#" aria-label="Previous"><span aria-hidden="true">首页</span></a></li>
                        <li class="active"><a href="#">1 <span class="sr-only">(current)</span></a></li>
                        <li><a href="#">2</a></li>
                        <li><a href="#">3</a></li>
                        <li><a href="#">4</a></li>
                        <li><a href="#">5</a></li>
                        <li><a href="#" aria-label="Next"><span aria-hidden="true">末页</span></a></li>
                    </ul>
                </div>
            </div>
            
        </div>

        <div class="container">
            <div class="row  mt-20 mb-30  bg-white">
                <img  src="/images/banner/footer.png" width="100%">
            </div>
        </div>
    </div>
    
    @include('layouts.footer')
@stop


