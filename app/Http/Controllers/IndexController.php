<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class IndexController extends Controller
{

    /**
     * IndexController constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 主页页面
     * @return mixed
     */
    public function index()
    {

        $params = [
            'page' =>'page-index',
        ];
        return View::make('index.index',$params);
    }
}
