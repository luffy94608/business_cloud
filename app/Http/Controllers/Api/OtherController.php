<?php

namespace App\Http\Controllers\Api;

use App\Http\Builders\OtherBuilder;
use App\Models\ApiResult;
use App\Models\Enums\ErrorEnum;
use App\Repositories\UserRepositories;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class OtherController extends Controller
{
    /**
     * OrderController constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    

    /**
     * 错误捕获
     * @param Request $request
     * @return mixed
     */
    public function trackError(Request $request)
    {
        $pattern = [
            'msg' => 'required',
        ];
        $this->validate($request, $pattern);
        $params = $request->only(array_keys($pattern));
        $path = storage_path('logs/client-error.log');
        $handle = new RotatingFileHandler($path);
        $handle->setFormatter(new LineFormatter(null, null, true));
        $logger = new Logger('client_error');
        $logger->pushHandler($handle);
        $logger->error($params['msg'], []);

        return response()->json((new ApiResult(0, ErrorEnum::transform(ErrorEnum::Success), []))->toJson());

    }

    /**
     * 用户日志记录
     * @param Request $request
     * @return mixed
     */
    public function trackUser(Request $request)
    {
        $pattern = [
            'log' => 'required',
        ];
        $this->validate($request, $pattern);
        $params = $request->only(array_keys($pattern));
        $params['log']['user_id'] = $this->uid;
        UserRepositories::insertUserLog($params['log']);
        return response()->json((new ApiResult(0, ErrorEnum::transform(ErrorEnum::Success), []))->toJson());

    }
}
