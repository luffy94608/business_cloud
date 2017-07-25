<?php
/**
 * Created by PhpStorm.
 * User: Kevin
 * Date: 2/1/16
 * Time: 3:27 PM
 */

return [
    'local' => ["partner_id"        => "2088411711508655",
        "seller_email"      => "liufuhua@hollo.cn",
        "sign_type"         => "RSA",
        "key"               => "lchauqnoooy04uf1j7ode8mpj9ctwudb",
        "notify_url"        => "http://hgt-dev.hollo.cn/api/v1/pay/server_alipay_notify",
        "bicycle_notify_url"      => "http://hgt-dev.hollo.cn/api/v2/bicycle/pay/server_alipay_notify",
        "dedicated_notify_url"      => "http://hgt-dev.hollo.cn/api/v2/booking/pay/server_alipay_notify",
        "public_key"        => "../certs/alipay_public_key.pem",
        "private_key"       => "../certs/alipay_private_key.pem"],
    'production' => [
        "partner_id"        => "2088411711508655",
        "seller_email"      => "liufuhua@hollo.cn",
        "sign_type"         => "RSA",
        "key"               => "lchauqnoooy04uf1j7ode8mpj9ctwudb",
        "notify_url"        => "http://hgt.hollo.cn/api/v1/pay/server_alipay_notify",
        "bicycle_notify_url"        => "http://hgt.hollo.cn/api/v2/bicycle/pay/server_alipay_notify",
        "dedicated_notify_url"        => "http://hgt.hollo.cn/api/v2/booking/pay/server_alipay_notify",
        "public_key"        => "../certs/alipay_public_key.pem",
        "private_key"       => "../certs/alipay_private_key.pem"
    ]
];