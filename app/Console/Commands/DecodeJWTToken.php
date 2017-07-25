<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Namshi\JOSE\JWS;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\JWT;
use Tymon\JWTAuth\Token;

class DecodeJWTToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jwt:decode {token}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '反解 jwt-token';

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
        $token = $this->argument('token');

//        $algo = config('jwt.algo');
//        $jws = new JWS(['typ' => 'JWT', 'alg' => $algo]);
//        try {
//            $jws = JWS::load($token);
//        } catch (\Exception $e) {
//            print_r($e->getMessage());
//        }
//
//        print_r($jws->getPayload()['sub'] . PHP_EOL);
        $payload = JWTAuth::manager()->decode(new Token($token));
        print_r($payload['sub'] . PHP_EOL);
    }
}
