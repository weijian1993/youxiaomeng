<?php
declare (strict_types=1);

namespace app\listener;

use app\consts\SystemConst;
use app\Request;
use app\service\AdminService;

class UserLog
{
    /**
     * 事件监听处理
     *
     * @return mixed
     */
    public function handle(Request $request)
    {
        if ($request->post() && (substr($request->pathinfo(), 0, 5) == 'admin')) {
            /** @var AdminService $adminService */
            $adminService = app('adminService');
            $filter   = [
                ['path', 'like', '%' . $request->pathinfo() . '%'],
                ['record', '=', SystemConst::AUTH_RECORD_ON],
                ['type', '=', SystemConst::AUTH_TYPE_BUTTON]
            ];
            $auth         = $adminService->getAuth([]);

            if (!empty($auth)) {
                $data = [
                    'user_id' => $request->userId,
                    'time'    => time(),
                    'ip'      => $request->ip(),
                    'path'    => $auth['path'],
                    'title'   => $auth['title'],
                    'params'  => json_encode($request->param())
                ];
                $adminService->addHandleLog($data);
            }
        }
        return true;
    }
}
