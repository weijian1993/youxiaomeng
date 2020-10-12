<?php
namespace app\controller\admin;

use app\BaseController;
use app\facade\GeoHelper;
use app\facade\TokenHelper;
use think\App;
use think\facade\Cookie;
use think\facade\Request;
use think\facade\View;

class Passport extends BaseController
{
    public $service;
    /**
     * 初始化
     * IndexController constructor.
     * @param App|null $app
     */
    public function __construct(App $app = null)
    {
        $service       = app('adminService');
        $this->service = $service;
        parent::__construct($app);
    }
    public function login()
    {
        if(Request::isAjax()){
            $filter = [
                'name'   => Request::post('username', null),
                'passwd' => Request::post('password', null),
            ];
            if (!$user = $this->service->getUser($filter)) {
                throw new \RuntimeException('账号密码不正确');
            }
            $token = TokenHelper::createToken($user['user_id']);
            Cookie::set('admin_token', $token, 86400);
            // 记录登陆信息
            $data = [
                'user_id'    => $user['user_id'],
                'time'       => time(),
                'ip'         => $this->request->ip(),
                'address'    => GeoHelper::getLocationByIp("36.5.110.44"),
                'user_agent' => $this->request->header('user-agent')
            ];
            $this->service->addLoginLog($data);
            $result = [
                'error' => 0,
                'msg'   => '',
                'data'  => $data,
                'url'   => $_SERVER["HTTP_REFERER"] ?? $_SERVER['SERVER_NAME'],
            ];
            return  $result;
        }else{
            return View::fetch();
        }

    }
    /**
     * 退出登陆
     */
    public function loginOut()
    {
        Cookie::delete('admin_token');
      return  $this->response();
    }

}
