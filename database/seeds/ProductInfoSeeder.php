<?php

use Illuminate\Database\Seeder;
use App\Models\ProductInfo;

class ProductInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'id'            =>1,
                'code'          =>'K001',
                'description'   =>'K001 快捷巴士车票',
                'content'       =>'哈罗同行 K001线路 快捷巴士车票',
                'fee'           =>200,
            ],
            [
                'id'            =>2,
                'code'          =>'K002',
                'description'   =>'K002 快捷巴士车票',
                'content'       =>'哈罗同行 K002线路 快捷巴士车票',
                'fee'           =>200,
            ],
            [
                'id'            =>3,
                'code'          =>'K003',
                'description'   =>'K003 快捷巴士车票',
                'content'       =>'哈罗同行 K003线路 快捷巴士车票',
                'fee'           =>200,
            ],
            [
                'id'            =>4,
                'code'          =>'K004',
                'description'   =>'K004 快捷巴士车票',
                'content'       =>'哈罗同行 K004线路 快捷巴士车票',
                'fee'           =>200,
            ],
            [
                'id'            =>5,
                'code'          =>'K005',
                'description'   =>'K005 快捷巴士车票',
                'content'       =>'哈罗同行 K005线路 快捷巴士车票',
                'fee'           =>200,
            ],
            [
                'id'            =>6,
                'code'          =>'K501',
                'description'   =>'K501 快捷巴士车票',
                'content'       =>'哈罗同行 K501线路 快捷巴士车票',
                'fee'           =>200,
            ],
            [
                'id'            =>7,
                'code'          =>'E001',
                'description'   =>'环保科技园班车',
                'content'       =>'环保科技园班车',
                'fee'           =>400,
            ],
            [
                'id'            =>8,
                'code'          =>'GZ101',
                'description'   =>'西北旺地铁--软件园二期--软件园一期',
                'content'       =>'西北旺地铁--软件园二期--软件园一期',
                'fee'           =>100,
            ],
            [
                'id'            =>9,
                'code'          =>'GZ102',
                'description'   =>'GZ102 西北旺地铁—软件园二期',
                'content'       =>'GZ102 西北旺地铁—软件园二期',
                'fee'           =>100,
            ],
            [
                'id'            =>10,
                'code'          =>'GZ103',
                'description'   =>'GZ103 西二旗地铁—软件园一期',
                'content'       =>'GZ103 西二旗地铁—软件园一期',
                'fee'           =>100,
            ],
            [
                'id'            =>11,
                'code'          =>'GW101',
                'description'   =>'GW101 软件园一期-软件园二期-西北旺地铁',
                'content'       =>'GW101 软件园一期-软件园二期-西北旺地铁',
                'fee'           =>100,
            ],
            [
                'id'            =>12,
                'code'          =>'GW103',
                'description'   =>'GW103 软件园一期--西二旗地铁',
                'content'       =>'GW103 软件园一期--西二旗地铁',
                'fee'           =>100,
            ],
        ];
        foreach ($data as $item) {
            $bannerModel = new ProductInfo();
            $bannerModel->id = $item['id'];
            $bannerModel->code = $item['code'];
            $bannerModel->description = $item['description'];
            $bannerModel->content = $item['content'];
            $bannerModel->fee = $item['fee'];
            $bannerModel->save();
        }
    }
}
