<?php

namespace App\Jobs;

use App\Helper\MapUtil;
use App\Models\Bicycle\BicycleDamageReportRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GeoReverseCodingOfDamageReport extends Job implements  ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $reportId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($reportId)
    {
        $this->reportId = $reportId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $reportId = $this->reportId;

        $report = BicycleDamageReportRecord::find($reportId);
        if ($report) {
            try {
                $location = $report->location;
                $result = MapUtil::reverseGeo([
                    'lat' => $location['lnglat'][1],
                    'lng' => $location['lnglat'][0]
                ]);

                if ($result) {
                    $location['name'] = $result['formatted_address'];
                    $report->location = $location;
                    $report->save(['timestamps'=>false]);
                }
            }
            catch (\Exception $e) {
                print_r('----------------- report location geo reverse coding failed: ' . $reportId . PHP_EOL);
                print_r($e->getMessage() . PHP_EOL);
            }
        }
    }
}
