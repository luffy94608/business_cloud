<?php

namespace App\Jobs;

use App\Helper\MapUtil;
use App\Models\Bicycle\BicyclePosition;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DealPositionNameJob extends Job implements  ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $para = null;
    protected $host = 'http://api.map.baidu.com/geocoder/v2/';
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $para)
    {
        //
        $this->para = $para;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $positionId = $this->para['position_id'];
        $loc = $this->para['loc'];

        $result = MapUtil::reverseGeo([
            'lat' => $loc[1],
            'lng' => $loc[0]
        ]);

        if ($result) {
            $position = BicyclePosition::find($positionId);
            $a = $position->location;
            $a['name'] = $result['formatted_address'];
            $position->location = $a;
            $position->save(['timestamps'=>false]);
        }

    }
}
