<?php
/**
 * Created by PhpStorm.
 * User: Kevin
 * Date: 12/18/15
 * Time: 6:23 PM
 */

namespace App\Http\Transformers;

use App\Models\Enums\HeartTypeEnum;
use App\Models\Heart;
use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;

class HeartTransformer extends TransformerAbstract
{

    private $params = [];

    function __construct($params = [])
    {
        //only_show_main_stations, user
        $this->params = $params;
    }

    /**
     * Turn this item object into a generic array.
     *
     * @param \App\Models\Heart $heart
     * @return array
     */
    public function transform(Heart $heart)
    {
        $basic = array(HeartTypeEnum::Version => 0);
        if (isset($heart->version)) {
            $basic[HeartTypeEnum::Version] = $heart->version;
        }

        return $basic;
    }
}
