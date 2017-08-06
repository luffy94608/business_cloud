<p>用户:{{ \App\Helper\Util::getUserName() }}</p>
<p>手机号:{{ \App\Helper\Util::getUserMobile() }}</p>
<p>关注地区: {!! \App\Http\Builders\UserBuilder::toUserFollowAreaHtml($data['area']) !!}</p>
<p>关注行业:
    <select disabled>
        {!!  \App\Http\Builders\DataBuilder::toIndustryLevelOneOptionHtml($data['industry'])  !!}
    </select></p>
<p>关注时间: 最近{{ $data['time'] }}个月</p>
<p>关注关键词: {!! \App\Http\Builders\UserBuilder::toUserFollowKeywordHtml($data['keyword']) !!}</p>
