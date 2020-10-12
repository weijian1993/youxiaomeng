<?php

namespace app\controller\admin;
use app\consts\SystemConst;
use think\App;
use think\facade\View;

class Index extends Common
{

    public $service;

    /**
     * 初始化
     * IndexController constructor.
     * @param App|null $app
     */
    public function __construct(App $app = null)
    {
        parent::__construct($app);
        $service       = app('adminService');
        $this->service = $service;
    }


    /**
     * 初始化页面
     * @return mixed
     */
    public function index()
    {
        $filter  = [
            'user_id' => $this->request->userId
        ];
        $user    = $this->service->getUser($filter);
        $filter  = [
            'type'   => SystemConst::AUTH_TYPE_MENU,
            'hidden' => SystemConst::AUTH_HIDDEN_OFF
        ];
        $fields  = [
            'auth_id', 'title', 'name', 'path', 'pid', 'icon', 'dateline'
        ];
        $orderby = [
            'orderby' => 'asc',
            'auth_id' => 'asc'
        ];
        $auths   = $this->service->getAuthList($filter, $fields, $orderby);
        $items   = formatTree($auths, 0, 1, ['pid' => 'pid', 'id' => 'auth_id', 'child' => 'child']);
        View::assign('user', $user);
        View::assign('items', $items);
        return View::fetch();
    }


    /**
     * 首页
     */
    public function home()
    {
        echo '暂未开发';
    }
}
