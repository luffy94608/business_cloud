<?php
/**
 * Created by PhpStorm.
 * User: rinal
 * Date: 3/10/16
 * Time: 7:03 PM
 */


namespace App\Tools\Message;

use App\Helper\Util;
use App\Models\Driver\Driver;
use App\Models\Enum\HeartTypeEnum;
use App\Models\Enum\HttpUrlEnum;
use App\Models\Enum\MessageEnum;
use App\Models\Enum\NotificationTypeEnum;
use App\Models\NotificationHistory;
use App\Models\PushToken;
use App\Models\SMSHistory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Log;
use MongoDB\BSON\ObjectID;

class MessageCenter
{

    public static function sendMessageAction(array $ids, $mess, $type)
    {
        if ($type == MessageEnum::SMS) {
            return MessageCenter::sendSMSByCenter($ids, $mess);
        } else {
            return MessageCenter::sendNotificationByCenter($ids, $mess, true);
        }

    }

    public static function sendSMSByCenter($mobiles, $mess) {
        if (!is_array($mobiles)) {
            $mobiles = [$mobiles];
        }
        $result = app('http_client')->postJson(HttpUrlEnum::SendSMS, [
            'app_id' => env('APP_MESSAGE_ID'),
            'mobiles' => $mobiles,
            'message' => $mess,
            'message_suffix' => env('APP_NAME')
        ]);

        foreach ($mobiles as $mobile) {
            $sms = new SMSHistory();
            $sms->mobile = $mobile;
            $sms->message = $mess;
            $sms->save();
        }

        return $result['code'] == 0;
    }

    /**
     * 发送通知
     * @param $ids
     * @param $mess  (type, content, url, act_url, target_id, extra_info)
     * @param $needPush
     * @return bool
     */
    public static function sendNotificationByCenter($ids, $mess, $needPush)
    {
        if (!is_array($ids)) {
            $ids = [$ids];
        }
        $msgType = $mess['type'];
        $objects = null;
        $appId = null;
        if ($msgType == NotificationTypeEnum::DriverSys) {
            $mIds = [];
            if (count($ids)) {
                foreach ($ids as $id) {
                    $mIds[] = new ObjectID($id);
                }
            }
            $objects = Driver::whereIn('_id', $mIds)->get();
            $appId = env('APP_DRIVER_ID');
        } else {
            $objects = User::whereIn('_id', $ids)->get();
        }
        $tokens = [];
        $notPushArr = [11, 41];
        $needPush = in_array($mess['type'], $notPushArr) ? 0 : 1;

        foreach ($objects as $u) {
            $pushToken = $u->pushToken;
            if (is_null($pushToken)) {
                $tokens[] = [
                    'user_id' => $u->id,
                    'token' => null
                ];
            } else {
                $tokens[] = [
                    'user_id' => $u->id,
                    'token' => $pushToken->token,
                    'push_type' => strtolower($pushToken->platform) == 'iphone' ? 'ios' : 'android'
                ];
            }
        }
        $messageContent = [
            'message_type' => $mess['type'] == 42 ? 41: $mess['type'],
            'message' => $mess['content'],
            'url' => ''
        ];
        if (array_key_exists('extra_info', $mess)) {
            $messageContent['extra_info'] = $mess['extra_info'];
        }

        if ($mess['type'] == 42) {
            $custom = ['bicycle_pay' => 1];
        } else {
            $custom = [];
        }

        if ($mess['type'] == 42 && count($ids) == 1) {
            $user = User::find($ids[0]);
            if ($user->current_app == 'BIKE') {
                $appId = '5861dab14105c08390d957e6';
            }
        }
        $result = app('http_client')->postJson(HttpUrlEnum::SendNotification, [
            'app_id' => is_null($appId) ? env('APP_MESSAGE_ID') : $appId,
            'push_tokens' => $tokens,
            'need_push' => $needPush,
            'message_content' => $messageContent,
            'custom' => $custom
        ]);

        foreach ($objects as $u) {
            $history = new NotificationHistory();
            $history->content = $mess['content'];
            $history->object_uid = $u->id;
            $history->url = array_key_exists('url', $mess) ? $mess['url'] : '';
            $history->act_url = array_key_exists('act_url', $mess) ? $mess['act_url'] : '';
            $history->status = 0;
            $history->index = -1;
            $history->timestamp = Carbon::now()->timestamp;
            $history->target_id = array_key_exists('target_id', $mess) ? $mess['target_id'] : null;
            $history->type = $mess['type'];
            if (array_key_exists('extra_info', $mess)) {
                $history->extra_info = $mess['extra_info'];
            }
            $history->save();

            Util::setHeartbeat($u, HeartTypeEnum::transform(HeartTypeEnum::HolloNotification));
        }

        return $result['code'] == 0;
    }

    public static function getNotification($past, $userId, $timestamp, $cursor, $limit=10, $driverType = false)
    {
        $result = app('http_client')->postJson(HttpUrlEnum::GetNotification, [
            'app_id' =>$driverType?  env('APP_DRIVER_ID') : env('APP_MESSAGE_ID'),
            'past' => $past,
            'user_id' => $userId,
            'timestamp' => $timestamp,
            'cursor' => $cursor,
            'limit' => $limit
        ]);
        $data['notifications'] = [];
        $time = 0;
        $msgData =  (isset($result['data']) && $result['code']) == 0 ?  $result['data']['notification_records'] : [];
        if ( count($msgData) ) {
            foreach ($msgData as $item) {
                $tmp = [
                    'type' => $item['message_content']['message_type'],
                    'content' => $item['message_content']['message'],
                    'cursor_id' => $item['cursor'],
                    'created_at' => $item['created_at'],
                    'extra_info' => $item['message_content']['extra_info'] ? $item['message_content']['extra_info'] : json_decode('{}')
                ];
                array_push($data['notifications'], $tmp);
            }
            $time = $result['data']['timestamp'];
        }
        $data['timestamp'] = $time;
        return $data;
    }
}
