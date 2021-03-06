<?php
/**
 * Created by PhpStorm.
 * User: luffy
 * Date: 20/02/2017
 * Time: 10:48
 */

namespace App\Repositories;                                  




use App\Models\Enums\WebsiteEnum;
use App\Models\HotWord;
use App\Models\RegionAgent;
use App\Models\Website;

class WebsiteRepositories
{
    /**
     * 获取当前页面的广告
     * @return string
     */
    public static function currentPageFooterAd()
    {
        $ad = '<img src="/images/banner/footer.png" width="100%">';
        $page = 0;
        $path = \Request::path();
        if ($path === '/') {
            $page = WebsiteEnum::IndexPage;
        } elseif ($path === 'bid-call') {
            $page = WebsiteEnum::PublishPage;
        } elseif ($path === 'bid-winner') {
            $page = WebsiteEnum::BidPage;
        } elseif ($path === 'rival') {
            $page = WebsiteEnum::CompetitorPage;
        } elseif ($path === 'search-list') {
            $page = WebsiteEnum::SearchPage;
        } elseif (stripos($path , 'rival-detail/') !== false) {
            $page = WebsiteEnum::DetailPage;
        }
        $result = Website::where('site', $page)
            ->where('status', 1)
            ->orderBy('id', -1)
            ->first();
        if (!is_null($result)) {
            $ad = sprintf('<img class="js_location_url" src="%s" data-target="_blank" data-url="%s"  width="100%%">', $result->img_url, $result->site_url);
        }
        return $ad;
    }

    /**
     * 首页热门关键词
     */
    public static function hotKeywords()
    {
        $words = HotWord::all();
        return $words;
    }

    /**
     * 区域代理信息
     * @param $id
     * @return mixed
     */
    public static function regionAgentDetail($id)
    {
        $agent = RegionAgent::where('area_id', $id)
            ->orderBy('id', -1)
            ->first();
        return $agent;
    }


}