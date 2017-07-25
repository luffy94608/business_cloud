<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MoloquentMigrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db_migrate:moloquent';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Python to PHP mongo migrate';

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
        $data = [
            'bus_path_schedules' => ['bus_path_id'],
            'bus_room_seats' => ['bus_room_id'],
            'bus_rooms' => ['bus_id', 'bus_path_schedule_id', 'driver_id'],
            'buses' => ['driver_id', 'path_id'],
            'bus_maintenance'=>['driver_id'],
            'charge_record'=>['driver_id'],

            'shuttle_schedules2' => ['driver_id', 'bus_id', 'shuttle_path_id'],
            'shuttles2' => ['driver_id', 'path_id', 'related_path_id'],
            'incident_notification' => ['driver_id'],

            'line_groups' => ['bus_path_id'],

            'order_contents' => ['line_id', 'shuttle_schedule_id'],
            'order_content_seats' => ['bus_room_id', 'content_id', 'bus_path_id', 'bus_path_schedule_id'],

            'tour_bus_seats' => ['line_id'],

            'me_comments' => ['order_content_seat_id', 'bus_room_id'],
            'user_company' => ['company_id'],
            'bus_paths' => ['companies'],
            'vote_paths' => ['companies'],
            'lock_seats' => ['path_id'],
            'driver_position_new' => ['bus_room_id']
        ];

        foreach ($data as $k => $v) {
            print_r(sprintf('Handling %s', $k) . PHP_EOL);
            $this->call('db_migrate:ObjectId', [
                'collection_name' => $k,
                'fields' => implode(',', $v)
            ]);
            print_r('----------------------------' . PHP_EOL);
        }
    }
}
