<?php

namespace App\Jobs;

use App\Helper\MapUtil;
use App\Models\Bicycle\BicycleTrack;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TransformBicycleTracks extends Job implements  ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    protected $orderId;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $first = BicycleTrack::where('order_id', $this->orderId)->orderBy('created_at', 'asc')->first();
        if (is_null($first)) {
            return;
        }
        $last = BicycleTrack::where('order_id', $this->orderId)->orderBy('created_at', 'desc')->first();
        $all = [$first, $last];
        foreach ($all as $a) {
            try{
                $result = MapUtil::reverseGeo([
                    'lat' => $a->location['lnglat'][1],
                    'lng' => $a->location['lnglat'][0]
                ]);

                if ($result) {

                    $b = $a->location;
                    $b['name'] = $result['formatted_address'];
                    $a->location = $b;
                    $a->save(['timestamps'=>false]);
                }
            }
            catch (\Exception $e) {
                print_r('-----------------bicycle track error' . $this->orderId . PHP_EOL);
                print_r($e->getMessage() . PHP_EOL);
            }
        }
    }
}
