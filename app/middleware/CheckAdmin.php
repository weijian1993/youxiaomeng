<?php

namespace app\middleware;


use app\consts\ErrorCodeConst;
use app\consts\SystemConst;
use app\facade\Jwt;
use app\facade\TokenHelper;
use app\service\AdminService;
use think\facade\Cookie;

class CheckAdmin
{
    /**
     * 权限验证
     * @param          $request
     * @param \Closure $next
     * @return mixed|\think\response\Redirect
     */
    public function handle($request, \Closure $next)
    {

        $legal  = [
            'admin.passport',
        ];
        $verify = true;
        $path   = request()->pathinfo();
        foreach ($legal as $v) {
            if (strstr($path, $v, true) !== false) {
                $verify = false;
                break;
            }
        }
        if ($verify == true) {
            /** @var \app\service\AdminService $service */
            $service = app('adminService');
            // 开始验证是否登陆
            if (!$token = Cookie::get('admin_token')) {
                throw new \RuntimeException('未登录无法访问', ErrorCodeConst::NOT_LOGIN);
            } else if (!$id = TokenHelper::parseToken($token)) {
                throw new \RuntimeException('未登录无法访问', ErrorCodeConst::NOT_LOGIN);
            } else {

                // 开始验证权限
                $service->checkRole($id, $path);
                // 初始化参数
                if ($request->userId) {
                    $request->userId = $id;
                } else {
                    $request->userId = $id;
                }

            }
        }
        $this->log($request);
        return $next($request);
    }


    public function log($request)
    {
        if ($request->post() && (substr($request->pathinfo(), 0, 5) == 'admin')) {
            /** @var AdminService $adminService */
            $adminService = app('adminService');
            $path         = path($request);
            $filter       = [
                ['path', 'like', '%' . $path . '%'],
                ['record', '=', SystemConst::AUTH_RECORD_ON],
                ['type', '=', SystemConst::AUTH_TYPE_BUTTON]
            ];
            $auth         = $adminService->getAuth($filter);

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
