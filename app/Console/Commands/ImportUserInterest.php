<?php

namespace App\Console\Commands;

use App\Models\DataBid;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportUserInterest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import_user_interest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'import_user_interest';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $profile = \DB::table('profiles')->get();
        if (!empty($profile))
        {
            foreach ($profile as $p)
            {
                \Log::debug('import user:'.$p->user_id);
                $this->handleSingleProfile($p);
            }
        }
    }

    private function handleSingleProfile($profile)
    {
        $fAreas = $profile->follow_area;
        $fAreasArray = explode(',',$fAreas);
        $fIndustry = $profile->follow_industry;

        //生成area map
        $areas = \DB::table('dic_district')->get(['id','name','parent_id']);
        $areaMap = [];
        foreach ($areas as $a)
        {
            $areaMap[$a->id] = $a;
        }
//        var_dump($areaMap);

        //生成industry map
        $indsturys = \DB::table('dic_industry')->get(['id','name','parent_id']);
        $indsturyMap = [];
        foreach ($indsturys as $in)
        {
            $indsturyMap[$in->id] = $in;
        }
//        var_dump($indsturyMap);

        $clusterRes = \DB::table('cluster_result')->where(function ($query) use ($fAreasArray,$fIndustry) {
            $query->whereIn('parent_area_id', $fAreasArray)
                ->orWhereIn('area_id', $fAreasArray)
                ->orWhere('industry_id', $fIndustry);
        })->get();
        if (!empty($clusterRes))
        {
            $tableMap = [];//table->ids 的map 

            $zbAreaMap = [];//标书id->areaids 的map
            $zbIndustryMap = [];//标书id->industryid 的map
            //cluster数据获取
            foreach ($clusterRes as $r)
            {
                $tableMap[$r->from_table][] = $r->from_id;
                $zbAreaMap[$r->from_id][] = $r->area_id == 0 ? $r->parent_area_id : $r->area_id;
                if ($r->industry_id > 0)
                {
                    $zbIndustryMap[$r->from_id] = $r->industry_id;
                }
            }

            //招标数据获取
            if (!empty($tableMap))
            {
                $tableNeedCols = ['id','type_id','zb_type','title','url','date','zhaobiaoren','zhongbiaoren','zhongbiaoren2','zhongbiaoren3','tze','zbhte','zbhte2','zbhte3'];
                foreach ($tableMap as $table=>$ids)
                {
                    $zbData = \DB::table($table)->whereIn('id',$ids)->get($tableNeedCols);

                    if (!empty($zbData))
                    {
                        $zhongbiaoInserts=[];
                        $zhaobiaoInserts=[];
                        
                        foreach ($zbData as $zbInfo)
                        {
                            $zbIndustry = [];
                            if (isset($zbIndustryMap[$zbInfo->id]))//有行业
                            {
                                $indstryId = $zbIndustryMap[$zbInfo->id];
                                $zbIndustry = $indsturyMap[$indstryId];
                            }
                            $areaIds = $zbAreaMap[$zbInfo->id];
                            $zbAreas = array_map(function($x) use ($areaMap) { return $areaMap[$x]; }, $areaIds);
                            foreach ($zbAreas as $oneArea)
                            {
                                if ($zbInfo->type_id == 1)//中标
                                {
                                    $zhongbiaoInserts[] = $this->generateZhongbiaoInsertData($profile->user_id,$oneArea,$zbIndustry,$zbInfo);
                                }
                                else
                                {
                                    $zhaobiaoInserts[] = $this->generateZhaobiaoInsertData($profile->user_id,$oneArea,$zbIndustry,$zbInfo);
                                }
                            }
                        }
                        $step = 300;
                        if (!empty($zhongbiaoInserts))
                        {
                            $count = count($zhongbiaoInserts);
                            for ($i = 0 ;$i<$count ; $i += $step)
                            {
                                $sliceLen = min($step,$count-$i);
                                $temp = array_slice($zhongbiaoInserts,$i,$sliceLen);
                                \DB::table('data_bid')->insert($temp);
                            }

                        }
                        if (!empty($zhaobiaoInserts))
                        {

                            $count = count($zhaobiaoInserts);
                            for ($i = 0 ;$i<$count ; $i += $step)
                            {
                                $sliceLen = min($step,$count-$i);
                                $temp = array_slice($zhaobiaoInserts,$i,$sliceLen);
                                \DB::table('data_publisher')->insert($temp);
                            }
                        }
                    }
                }
            }
        }

    }

    //生成中标插入数据
    private function generateZhongbiaoInsertData($uid,$area,$industry,$data)
    {
        $insertData = [
            'user_id'=>$uid,
            'title'=> $data->title!= null ? $data->title :'',
            'url'=> $data->url!= null ? $data->url :'',
            'publisher'=> $data->zhaobiaoren!= null ? $data->zhaobiaoren :'',
            'budget'=> $data->tze!= null ? $data->tze :0,
            'bid_time'=>Carbon::createFromFormat('Y-m-d',trim($data->date))->copy()->timestamp,
            'created_at'=>Carbon::createFromFormat('Y-m-d',trim($data->date))->copy(),
        ];
//        if (!empty($area))
        {
            $insertData['area_id'] = $area->id;
            $insertData['area_text'] = $area->name;
        }
//        if ($industry)
        {
            $insertData['industry_id'] = $industry?$industry->id : 0;
            $insertData['industry_text'] = $industry?$industry->name : '';
        }
//        if ($data->zhongbiaoren != null)
        {
            $insertData['candidate1'] = $data->zhongbiaoren;
            $insertData['price1'] = $data->zbhte;
            $insertData['bid_company'] = $data->zhongbiaoren;
            $insertData['bid_price'] = $data->zbhte?: 0;
        }
//        if ($data->zhongbiaoren2 != null)
        {
            $insertData['candidate2'] = $data->zhongbiaoren2;
            $insertData['price2'] = $data->zbhte2 ?: 0;
        }
//        if ($data->zhongbiaoren3 != null)
        {
            $insertData['candidate3'] = $data->zhongbiaoren2;
            $insertData['price3'] = $data->zbhte3?: 0;
        }
        return $insertData;
    }

    //生成招标插入数据
    private function generateZhaobiaoInsertData($uid,$area,$industry,$data)
    {
        $insertData = [
            'user_id'=>$uid,
            'title'=> $data->title!= null ? $data->title :'',
            'url'=> $data->url!= null ? $data->url :'',
            'publisher'=> $data->zhaobiaoren!= null ? $data->zhaobiaoren :'',
            'budget'=> $data->tze!= null ? $data->tze :0,
//            'bid_time'=>Carbon::createFromFormat('Y-m-d',trim($data->date))->copy()->timestamp,
            'created_at'=>Carbon::createFromFormat('Y-m-d',trim($data->date))->copy(),
        ];

        $insertData['area_id'] = $area->id;
        $insertData['area_text'] = $area->name;

//        if ($industry)
        {
            $insertData['industry_id'] = $industry?$industry->id : 0;
            $insertData['industry_text'] = $industry?$industry->name : '';
        }


        $insertData['type'] = $data->zb_type;
        return $insertData;
    }

}
