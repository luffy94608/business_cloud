<?php
/**
 * Created by PhpStorm.
 * User: jet
 * Date: 2017/8/23
 * Time: 下午1:30
 */

namespace App\Tools\DataCluster;


class IndustryFilter
{
    private $table;
    private $totalRows;
    private $page = 0;
    private $pageNum = 100;

    //1 工程
    private $gongcheng = array('工程','混凝土','材料','运输','计算机','装修','施工','线路','建筑','建筑物','维修','保养','印刷','安装','通信','仪器','仪表','识别','建筑防水','排水','物业','房地产','采矿','房屋','勘察','水运','水利','住房','监理','大理石', '装配', '沥青','建设');
    //2 交通
    private $jiaotong = array('交通','公路','商务车','客船','车','公路','铁路','民航');
    //3 教育
    private $jiaoyu = array('经济学','研究','试验','文教','教具','教学','教育','图书','实验室','课本','文献');
    //4 医疗
    private $yiliao = array('医疗','健康','临床','医院','医药','急救','治疗','医用','诊断','药品','手术','助残');
    //5 农业
    private $nongye = array('食用','农业','肥料','农林','大豆','鱼','农药','消毒剂','苗木','毛皮','猪','乳制品', '奶',  '育苗','植物' );
    //6 机电设备
    private $jidianshebei = array('设备','机械','仪器','用具','内燃机','电泵','录像机','车辆','床类','产品','货物','电梯','空调','架','钢管','LED','机组','发电','热水器','电能','家具', '柜', '香蕉','桌', '制服', '窗帘','椅', '终端', '消毒剂', '寝具', '手提包', '背包', '饮水', '照相机', '投影', '工具', '避雷器', '电线', '电缆', '电源',  '纺织','橡胶','能源');
    //7 政府机构
    private $zhengfujigou = array('政府','市内','消防','市政','公共','事业单位','江','湖','环境','路灯','空气','园林','行政单位','城市','公路','造林','防洪','气象');
    //8 文化
    private $wenhua = array('旅游','博物馆','体育','文物','文化','艺术','娱乐','文艺','广播','电视','电影','新闻','球类','图书','乐器','音响','音像');
    //9 金融
    private $jinrong = array('保险','咨询');
    //10 服务
    private $fuwu = array('设计','服务','技术','产品','互联网','平台','软件','会展','数据','信息','视频','智能化','系统','安全','运营','测试','软件','防火墙','企业');

    public function resetAll($table){
        \DB::table('cluster_result')->where('from_table',$table)->update(['industry_id'=>0]);
        \DB::table('cluster_industry_no_match')->where('from_table',$table)->delete();
    }

    public function filterIndustry($table){
        $this->table = $table;
        $condition = "id,hangye,title,type_id,title,zhaobiaoren,zhongbiaoren";
        $this->totalRows = \DB::table($table)->count();

        while (($this->page * $this->pageNum) < $this->totalRows){
            $sql = "select %s from %s order by id asc limit %d,%d";
            $sql = sprintf($sql,$condition,$table,$this->page * $this->pageNum,$this->pageNum);
            $rs = \DB::select($sql);
            //TODO:filter
            $this->doFilter($rs);

            $this->page++;
//            if ($this->page == 2)
//                break;
        }
    }

    private function doFilter($data){
        if (!empty($data)){
            foreach ($data as $v){
                if (empty($v->hangye)){
                    $keyword = $v->title;
                }else{
                    $keyword = $v->hangye;
                }
                $industryId = $this->matchKeyWord($keyword);
                var_dump($industryId);
                $title = $v->title;//strlen($v->title) > 200 ? substr($v->title,0,200) : $v->title ;
                $zhaobiaoren = $v->zhaobiaoren;//strlen($v->zhaobiaoren) > 100 ? substr($v->zhaobiaoren,0,100) : $v->zhaobiaoren;
                $zhongbiaoren = $v->zhongbiaoren;//strlen($v->zhongbiaoren) > 200 ? substr($v->zhongbiaoren,0,200) : $v->zhongbiaoren ;
                if ($industryId > 0 ){
                    \DB::table('cluster_result')
                        ->where('from_table',$this->table)
                        ->where('from_id',$v->id)
                        ->update([
                            'industry_id'=>$industryId,
                            'type_id'=>$v->type_id,
                            'title'=>$title,
                            'zhaobiaoren'=>trim($zhaobiaoren),
                            'zhongbiaoren'=>trim($zhongbiaoren),
                        ]);
                }else{
                    if (!empty($v->hangye)){
                        \DB::table('cluster_industry_no_match')->insert([
                            'from_table'=>$this->table,
                            'from_id'=>$v->id,
                            'industry_value'=>$v->hangye,
                        ]);
                    }
                }

            }
        }

    }

    private function matchKeyWord($key){
        foreach ($this->yiliao as $v){
            $result = mb_strpos($key,$v);
            if ($result !== FALSE){
                return 4;
            }
        }

        foreach ($this->jiaoyu as $v){
            $result = mb_strpos($key,$v);
            if ($result !== FALSE){
                return 3;
            }
        }

        foreach ($this->jinrong as $v){
            $result = mb_strpos($key,$v);
            if ($result !== FALSE){
                return 9;
            }
        }

        foreach ($this->nongye as $v){
            $result = mb_strpos($key,$v);
            if ($result !== FALSE){
                return 5;
            }
        }

        foreach ($this->zhengfujigou as $v){
            $result = mb_strpos($key,$v);
            if ($result !== FALSE){
                return 7;
            }
        }

        foreach ($this->wenhua as $v){
            $result = mb_strpos($key,$v);
            if ($result !== FALSE){
                return 8;
            }
        }
        foreach ($this->jiaotong as $v){
            $result = mb_strpos($key,$v);
            if ($result !== FALSE){
                return 2;
            }
        }
        foreach ($this->fuwu as $v){
            $result = mb_strpos($key,$v);
            if ($result !== FALSE){
                return 10;
            }
        }
        foreach ($this->gongcheng as $v){
            $result = mb_strpos($key,$v);
            if ($result !== FALSE){
                return 1;
            }
        }
        foreach ($this->jidianshebei as $v){
            $result = mb_strpos($key,$v);
            if ($result !== FALSE){
                return 6;
            }
        }

        return 0;

    }
}