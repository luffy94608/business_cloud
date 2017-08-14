<div class="container-fluid bc-banner">
    <img class="pic hidden-xs" src="/images/banner/banner.png" width="100%">
    <img class="pic visible-xs" src="/images/banner/banner_mobile.png" width="100%">
    <div class="container text-center bcb-content">
        <div class="row">
            <div class="col-sm-8 col-sm-offset-2">
                <div class="col-xs-12">
                    <div class="input-mask"><span class="b-icon-search"></span>搜一搜</div>
                    <input type="text" class="form-control input-lg bcb-search" placeholder="" >
                    <div class="bcb-word">
                        {!! \App\Http\Builders\OtherBuilder::toBuildBannerKeywordHtml() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>