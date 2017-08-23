<?php

namespace App\Console\Commands;

use App\Tools\DataCluster\AreaFilter;
use App\Tools\DataCluster\CompanyFilter;
use App\Tools\DataCluster\IndustryFilter;
use Illuminate\Console\Command;

class ClusterZBData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cluster_data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '聚合招投标数据';

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
//        $areaFilter =  new AreaFilter();
//        $areaFilter->resetAll('tender_zb_guizhou_2');
//        $areaFilter->filterArea('tender_zb_guizhou_2',24);
//
//        $industryFilter = new IndustryFilter();
//        $industryFilter->resetAll('tender_zb_guizhou_2');
//        $industryFilter->filterIndustry('tender_zb_guizhou_2');

        $industryFilter = new CompanyFilter();
        $industryFilter->resetAll('tender_zb_guizhou_2');
        $industryFilter->filterCompany('tender_zb_guizhou_2');
    }
}
