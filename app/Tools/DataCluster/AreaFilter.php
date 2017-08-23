<?php
namespace App\Tools\DataCluster;
/**
 * Created by PhpStorm.
 * User: jet
 * Date: 2017/8/23
 * Time: 上午11:31
 */
class AreaFilter
{
    private $page = 0;
    private $pageNum = 1000;
    private $totalRows;
    private $table;
    private $provinceId;
    private $provinceName;

    public function resetAll($table){
        \DB::table('cluster_result')->where('from_table',$table)->delete();
        \DB::table('cluster_offset')->where('table_name',$table)->delete();
        \DB::table('cluster_area_failed')->where('from_table',$table)->delete();
    }

    public function filterArea($table,$provinceId){
        $this->provinceId = $provinceId;
        $rs = \DB::table('dic_district')
            ->select(['id','name','extra','suffix'])
            ->where('id',$this->provinceId)
            ->first();
        $this->provinceName = $rs->name.$rs->extra.$rs->suffix;

        $this->table = $table;
        $condition = "id,webid,big_area,quxian";
        $count = \DB::table($table)->count();
        $this->totalRows = $count;

        while (($this->page * $this->pageNum) < $this->totalRows){
            $sql = "select %s from %s order by id asc limit %d,%d";//where quxian != ''
            $sql = sprintf($sql,$condition,$table,$this->page * $this->pageNum,$this->pageNum);
//            $this->db->query($sql);
            $rs = \DB::select($sql);
            //TODO:filter
            $this->doFilter($rs);

            $this->page++;
//            $sql = "REPLACE cluster_offset set table_name = '%s',offset = %d";
//            $sql = sprintf($sql,$this->table,$this->page*$this->pageNum,$this->table);
//            var_dump($sql);
//            $this->db->query($sql);
            \DB::table('cluster_offset')->updateOrInsert(['table_name'=>$this->table],['table_name'=>$this->table,'offset'=>$this->page*$this->pageNum]);
//            if ($this->page == 2)
//                break;
        }

    }

    public function suffixFiler($data){
        $result = array();

        if (!empty($data)){
            //寻找二级
            if (mb_strpos($data,$this->provinceName) === 0){
                $data = mb_substr($data,mb_strlen($this->provinceName),mb_strlen($data)-1);
            }

            $sql = "select id,name,extra,suffix from dic_district where parent_id = ".$this->provinceId;
//            $this->db->query($sql);
            $rs = \DB::select($sql);

            foreach ($rs as $v){
                //最宽松匹配
                if(mb_strpos($data,$v->name) === 0){
                    //记录市级id作为候选结果
                    $result['city']['id'] = $v->id;
                    $result['city']['value'] = $v->name.$v->extra.$v->suffix;

                    $data = mb_substr($data,mb_strlen($v->name),mb_strlen($data)-1);
                    break;
                }
            }


            //寻找三级id
            if (isset($result['city'])) {
                $sql = "select * from dic_district where parent_id=" . $result['city']['id'];
//                $this->db->query($sql);
//            var_dump($sql);
                $rs = \DB::select($sql);
                if (!empty($rs)){
                    foreach ($rs as $v) {
                        //最宽松匹配
                        if (mb_strpos($data, $v->name) > 0) {
                            //记录市级id作为候选结果
                            $result['district']['id'] = $v->id;
                            $result['district']['value'] = $v->name.$v->extra.$v->suffix;

                            $data = mb_substr($data, mb_strlen($v->name), mb_strlen($data) - 1);
                            break;
                        }
                    }
                }
            }
        }

        var_dump($result);
        return $result;

    }
    public function doFilter($rs){
        foreach ($rs as $v){
//            var_dump($v);
            
            $districtName = $v->quxian;
            var_dump($v->id);
            $result = $this->suffixFiler(trim($districtName));
            $sql = "INSERT INTO cluster_result(from_table,from_id,parent_area_id,area_id) VALUES ('%s',%d,%d,%d)";
            $insertData = [
                'from_table'=>$this->table,
                'from_id'=>$v->id,
                'parent_area_id'=>$this->provinceId,
            ];
            if (isset($result['district'])){
                $insertData['area_id'] = $result['district']['id'];
                $sql = sprintf($sql,$this->table,$v->id,$this->provinceId,$result['district']['id']);
            }else if (isset($result['city'])){
                $insertData['area_id'] = $result['city']['id'];
                $sql = sprintf($sql,$this->table,$v->id,$this->provinceId,$result['city']['id']);
            }else{
                if (!empty(trim($v->quxian))){
//                    $sql1 = "REPLACE into cluster_area_failed(from_table,district_name) VALUES('%s','%s')";
//                    $sql1 = sprintf($sql1,$this->table,trim($v['quxian']));
//                    $id = $this->db->insert($sql1);
                    \DB::table('cluster_area_failed')->updateOrInsert(['from_table'=>$this->table,'district_name'=>trim($v->quxian)],['from_table'=>$this->table,'district_name'=>trim($v->quxian)]);
                }
                $sql = sprintf($sql,$this->table,$v->id,$this->provinceId,0);
                $insertData['area_id'] = 0;
            }
//            $id = $this->db->insert($sql);
            \DB::table('cluster_result')->insert($insertData);
//            echo $id."\n";
        }
    }
}