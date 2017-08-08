<?php

namespace App\Models\Enums;


class WebsiteEnum
{

    const IndexPage                     = 1;
    const PublishPage                   = 2;
    const BidPage                       = 3;
    const CompetitorPage                = 4;
    const SearchPage                    = 5;
    const DetailPage                    = 6;



    public static function transform($key)
    {
        $transformMap = array(
            self::IndexPage                         => "首页",
            self::PublishPage                       => "招标信息",
            self::BidPage                           => "中标信息",
            self::CompetitorPage                    => "竞争对手",
            self::SearchPage                        => "搜索页面",
            self::DetailPage                        => "详情页面",
        );
        return array_key_exists($key, $transformMap) ? $transformMap[$key] : '';
    }
}
