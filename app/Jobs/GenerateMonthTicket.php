<?php

namespace App\Jobs;

use App\Models\Line;
use App\Models\Schedule;
use App\Models\SeatStatusEnum;
use App\Models\StationScheduleSeat;
use App\Models\TicketStatusEnum;
use App\Models\UserTicket;
use App\Tools\Seat\SeatTool;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateMonthTicket extends Job implements  ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $params;

    /**
     * Create a new job instance.
     *
     * @param $params
     */
    public function __construct($params)
    {
        $this->params = $params;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $params = $this->params;

        $orderId = $params['order_id'];
        $lineId = $params['line_id'];
        $seatNum = $params['seat_number'];
        $uid = $params['uid'];
        $onStationId = $params['on_station_id'];
        $offStationId = $params['off_station_id'];

        $line = Line::find($lineId);
        if (is_null($line)) return;

        if (!isset($line->daily_lines)) return;

        foreach ($line->daily_lines as $k => $v) {
            $dailyLine = Line::find($v);
            if (is_null($dailyLine)) continue;

            $onStationId = null;
            $offStationId = null;

            $mainStations = [];
            foreach ($dailyLine->stations as $station) {
                if (isset($station['_id'])) {
                    $mainStations[] = $station;
                }
            }

            $onStationId = $mainStations[0]['_id'];
            $offStationId = $mainStations[count($mainStations) - 1]['_id'];

            $this->generateTickets($orderId, $v, $uid, $onStationId, $offStationId, $seatNum);
        }

    }

    public function generateTickets($orderId, $lineId, $uid, $onStationId, $offStationId, $seatNum)
    {
        $monthDt = Carbon::today()->endOfMonth()->addDay();
        $dayStartOfMonth = clone $monthDt->startOfMonth();
        $dayEndOfMonth = clone $monthDt->endOfMonth();
        $schedules = Schedule::where('line_id', $lineId)
            ->where('dept_at', '>', $dayStartOfMonth)
            ->where('dest_at', '<', $dayEndOfMonth)
            ->get();
        foreach ($schedules as $schedule) {
            $scheduleId = $schedule->id;

            $onStationSchedule = StationScheduleSeat::where('schedule_id', $scheduleId)
                ->where('station_id', $onStationId)
                ->first();

            $offStationSchedule = StationScheduleSeat::where('schedule_id', $scheduleId)
                ->where('station_id', $offStationId)
                ->first();

            $deptAt = $onStationSchedule ? $onStationSchedule->dept_at : $schedule->dept_at;
            $destAt = $offStationSchedule ? $offStationSchedule->dept_at : $schedule->dest_at;

            // 先锁上才能确认
            SeatTool::updateSeatStatus(
                $schedule,
                $seatNum,
                SeatStatusEnum::LOCKED,
                $uid,
                $onStationId,
                $offStationId
            );

            SeatTool::updateSeatStatus(
                $schedule,
                $seatNum,
                SeatStatusEnum::CONFIRMED,
                $uid,
                $onStationId,
                $offStationId
            );

            UserTicket::create([
                'id' => uniqid(),
                'type' => 'shuttle',
                'order_id' => $orderId,
                'seat_number' => $seatNum,
                'status' => TicketStatusEnum::PAID,
                'schedule_id' => $scheduleId,
                'on_station_id' => $onStationId,
                'off_station_id' => $offStationId,
                'user_id' => $uid,
                'use_coupon' => 0,
                'ticket_no' => $this->generateTicketNo(),
                'dept_at' => $deptAt,
                'dest_at' => $destAt
            ]);
        }
    }

    private function generateTicketNo()
    {
        $t = Carbon::now()->format('YmdHis');
        $e = mt_rand(10000, 99999);
        return $t . strval($e);
    }
}
