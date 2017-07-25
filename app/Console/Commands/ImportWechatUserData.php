<?php

namespace App\Console\Commands;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use GuzzleHttp;

class ImportWechatUserData extends Command
{
    protected $appId = null;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import_wechat_user_data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '微信用户数据导入';

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

        $userArray = DB::connection('wechat')->table('user_info')->where('Fuid', '>', '0')->get()->chunk(200);

        foreach ($userArray as $userChuck) {
            $insertData = [];
            foreach ($userChuck as $user) {
                if (empty($user)) {
                    continue;
                }
//                $userInfo = DB::connection('wechat')->table('user_info')->where('Fuid', $user->Fuid)->orderBy('Fopen_id', -1)->first();
//                if (is_null($userInfo)) {
//                    continue;
//                }
                $insertData[] = [
                    'open_id' => $user->Fopen_id,
                    'uid' => $user->Fuid,
                    'token' => '',
                    'expire_time' => 0,
                ];
            }
            //插入chunk条数据
            if (!empty($insertData)) {
                if (User::insert($insertData)) {
                    $this->info(count($insertData) . '条数据插入成功');
                }
            }
        }

        return $this->info('更新完毕');
    }


}
