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
    protected $signature = 'cluster_data {table} {type} {area}';

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
        $table = $this->argument('table');
        $type = $this->argument('type');
        $area = $this->argument('area');
        switch ($type)
        {
            case 'area':
            {
                $areaFilter =  new AreaFilter();
                $areaFilter->resetAll($table);
                $areaFilter->filterArea($table,$area);
                break;
            }
            case 'industry':
            {
                $industryFilter = new IndustryFilter();
                $industryFilter->resetAll($table);
                $industryFilter->filterIndustry($table);
                break;
            }
            case 'company':
            {
                $industryFilter = new CompanyFilter();
                $industryFilter->resetAll($table);
                $industryFilter->filterCompany($table);
                break;
            }
        }
        //

//



    }
}
