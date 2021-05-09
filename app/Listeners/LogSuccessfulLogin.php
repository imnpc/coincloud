<?php

namespace App\Listeners;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SigninLog;

class LogSuccessfulLogin
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $time = now();
        $ip = $this->request->ip();

        // 用户表记录用户最后登录时间和 IP
        $user = User::find($event->userId);
        $user->last_login_at = $time;
        $user->last_login_ip = $ip;
        $user->save();

        // 写入用户登录日志
        $login_ip_area = '';
        $area = geoip()->getLocation($ip)->toArray();
        if ($area) {
            if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE|FILTER_FLAG_NO_RES_RANGE)) {
                $login_ip_area = "intranet IP";
            } else {
                $login_ip_area = $area['country'] . '/' . $area['city'];
            }
        }

        SigninLog::create([
            'user_id' => $event->userId,
            'ip' => $ip,
            'ip_area' => $login_ip_area,
            'login_at' => $time,
            'client_id' => $event->clientId,
        ]);
    }
}
