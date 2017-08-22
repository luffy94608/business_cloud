<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ScanCompanyInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scan_company_info';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '扫描公司信息';

    private  $token = '';

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
        $companyNames = \DB::table('tender_zb_guizhou_2')->distinct('zhongbiaoren')->limit(800)->get();
        $this->token = $this->getToken();

        $nameMaps = [];
        foreach ($companyNames as $c)
        {
            $str = str_replace('（','(',$c->zhongbiaoren);
            $str = str_replace('）',')',$str);
            $nameMaps[trim($str)] = 1;
        }


        $count = count($companyNames);
        $step = 10;
        for ($i = 0; $i< $count; $i+= $step)
        {
            $len = min($step,$count-$i);
            $data = array_slice($nameMaps,$i,$len);
            $this->handleCompanyInfo($data);
        }

    }

    private function handleCompanyInfo($nameMaps)
    {
        if (!empty($nameMaps))
        {
            var_dump($nameMaps);
            $exsits = \DB::table('company_infos')->select(['company_name'])->whereIn('company_name',array_keys($nameMaps))->get();
            if (!empty($exsits))
            {
                foreach ($exsits as $e)
                {
                    unset($nameMaps[$e->company_name]);
                }
            }

            var_dump($nameMaps);
            if (!empty($nameMaps))
            {
                foreach ($nameMaps as $k=>$v)
                {
                    $res =  $this->queryCompany($k);
                    if (!empty($res))
                    {
                        foreach ($res as $r)
                        {
                            $insertRes =  \DB::table('company_infos')->updateOrInsert(['company_name'=>$r['company_name']],$r);
                            var_dump($insertRes.'company name: '.$r['company_name']);
                        }
                    }
                }
            }
        }
    }
    
    private function queryCompany($name)
    {
        $pos1 = strpos($name,'(');
        $pos2 = strpos($name,'（');
        if (strlen(trim($name)) == 0 || strlen(trim($name)) > 100 || $pos1 === 0 || $pos2 === 0)
        {
            return [];
        }
        $client = new \GuzzleHttp\Client();
        var_dump('query name :'.$name);
        $res = $client->request('GET', 'http://api.qianzhan.com/OpenPlatformService/OrgCompany',[
            'query'=>[
                'token'=>$this->token,
                'type'=>'JSON',
                'companyName'=>$name,
                'areaCode'=>'',
                'page'=>1,
                'pagesize'=>10
            ]
        ]);
        $ret = array();

        if ($res->getStatusCode() == 200)//成功
        {
            $result= $res->getBody();
            $resArray = json_decode($result,true);
            $dataList = $resArray['result'];
            if (!empty($dataList))
            {
                var_dump('query code,msg :'.$resArray['status'].$resArray['message']);
                if ($resArray['status'] == 101 || $resArray['status'] == 102)//token过期
                {
                    $this->token = $this->getToken();
                    return $this->queryCompany($name);
                }

                $idx = 0;
                foreach ($dataList as $d)
                {
                    $ret[] = [
                        'company_code' => $d['companyCode'],
                        'company_key'=>$d['companyKey'],
                        'company_name'=>$idx == 0 ? $name :$d['companyName'],
                        'reg_number'=>$d['regNumber'],
                        'credit_code'=>$d['creditCode'],
                        'area_code'=>$d['areaCode'],
                        'area_name'=>$d['areaName'],
                        'company_type'=>$d['companyType'],
                        'issue_time'=>$d['issueTime'],
                        'reg_org_name'=>$d['regOrgName'],
                        'bussiness_des'=>$d['bussinessDes'],
                        'business_status'=>$d['businessStatus'],
                        'fa_ren'=>$d['faRen'],
                        'reg_m'=>$d['regM'],
                        'reg_money'=>$d['regMoney'],
                        'reg_type'=>$d['regType'],
                        'address'=>$d['address'],
                    ];
                    $idx++;
                }
            }
            else
            {
                $ret[] = [
                    'company_name'=>$name,
                ];
            }

        }
        else
        {
            $result= $res->getBody();
            $resArray = json_decode($result,true);
            var_dump('query code,msg :'.$resArray['status'].$resArray['message']);
        }
        return $ret;
    }

    private function getToken()
    {
        $client = new \GuzzleHttp\Client();
        $res = $client->request('GET', 'http://api.qianzhan.com/OpenPlatformService/GetToken',[
            'query'=>[
                'type'=>'JSON',
                'appkey'=>'3d0b52c6555d850c',
                'seckey'=>'4afd7013afff8ecd',
            ]
        ]);
        if ($res->getStatusCode() == 200)//成功
        {
            $result= $res->getBody();
            $resArray = json_decode($result,true);
            $token = $resArray['result']['token'];
            return $token;
        }
        return '';
    }
}
