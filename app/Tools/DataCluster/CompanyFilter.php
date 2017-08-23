<?php
/**
 * Created by PhpStorm.
 * User: jet
 * Date: 2017/8/23
 * Time: 下午1:43
 */

namespace App\Tools\DataCluster;


class CompanyFilter
{
    private $table;
    private $totalRows;
    private $page = 0;
    private $pageNum = 100;

    public function resetAll($table){
        \DB::table('cluster_result')->where('from_table',$table)->update(['company_id'=>0]);
    }

    public function filterCompany($table){
        $this->table = $table;
        $this->totalRows = \DB::table($table)->count();

        while (($this->page * $this->pageNum) < $this->totalRows){
            $rs = \DB::table($table)->select(['id','zhongbiaoren','title'])->orderBy('id')->offset($this->page * $this->pageNum)->limit($this->pageNum)->get();
            //TODO:filter
            $this->doFilter($rs);

            $this->page++;
        }
    }

    private function doFilter($data){
        if (!empty($data)){
            foreach ($data as $v){
                $companyStr = $this->formatCompanyName($v->zhongbiaoren);
                if (empty($companyStr)){
                    continue;
                }
                var_dump($companyStr);
                $company = \DB::table('company_infos')->where('company_name',$companyStr)->first(['id','company_name']);

                if ($company){
                    \DB::table('cluster_result')
                        ->where('from_table',$this->table)
                        ->where('from_id',$v->id)
                        ->update(['company_id'=>$company->id]);
                }
            }
        }
    }

    private function formatCompanyName($name)
    {
        $pos1 = strpos($name,'(');
        $pos2 = strpos($name,'（');
        if (strlen(trim($name)) == 0 || strlen(trim($name)) > 100 || $pos1 === 0 || $pos2 === 0)
        {
            return '';
        }
        $str = str_replace('（','(',$name);
        $str = str_replace('）',')',$str);
        return $str;
    }
}