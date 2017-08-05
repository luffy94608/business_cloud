<?php
/**
 * Created by PhpStorm.
 * User: luffy
 * Date: 20/02/2017
 * Time: 10:48
 */

namespace App\Repositories;                                  





use App\Models\BusinessAnalysis;
use App\Models\CompanyAnalysis;

class AnalysisRepositories
{
    /**
     * 企业分析
     * @param $data
     * @return bool
     */
    public static function insertCompanyAlys($data)
    {
        $model = new CompanyAnalysis();
        return BaseRepositories::updateOrInsert($model, $data)? $model->id :false;
    }

    /**
     * 市场分析
     * @param $data
     * @return bool
     */
    public static function insertBusinessAlys($data)
    {
        $model = new BusinessAnalysis();
        return BaseRepositories::updateOrInsert($model, $data)? $model->id :false;
    }
}