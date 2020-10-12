<?php

namespace app\controller\admin\admin;

use app\consts\SystemConst;

use app\controller\admin\Common;
use think\facade\View;

/**
 * Class UserController
 * @package app\controller\admin\admin
 * @property \app\service\AdminService $service
 */
class Handle extends Common
{

    /**
     * 初始化页面
     */
    public function initialize()
    {
        parent::initialize();
        $service       = app('adminService');
        $this->service = $service;
    }

    /**
     * 列表
     * @return mixed
     */
    public function index()
    {
        if ($this->request->isAjax()) {
            $filter = [];
            if ($SO = $this->request->param('SO')) {
                if (!empty($SO['user_id'])) {
                    $filter[] = [
                        'user_id', '=', $SO['user_id']
                    ];
                }
                if (!empty($SO['dateline'])) {
                    $times    = explode('-', $SO['dateline']);
                    $stime    = strtotime(str_replace('/', '-', $times[0]));
                    $ltime    = strtotime(str_replace('/', '-', $times[1]));
                    $filter[] = [
                        'time', 'between', [$stime, $ltime]
                    ];
                }
            }
            $orderby = [
                'time' => 'desc'
            ];
            $page    = $this->request->param('page', 1);
            $limit   = $this->request->param('limit', SystemConst::DEFAULT_LIMIT);
            $items   = $this->service->getHandleLogList($filter, [], $orderby, $page, $limit);
            $count   = $this->service->getHandleLogCount($filter);
            $filter  = [
                ['user_id', 'in', array_column($items, 'user_id')]
            ];
            $users   = $this->service->getUserList($filter);
            $users   = array_column($users, null, 'user_id');
            $filter  = [
                ['role_id', 'in', array_column($users, 'role_id')]
            ];
            $roles   = $this->service->getRoleList($filter);
            $roles   = array_column($roles, null, 'role_id');
            foreach ($items as &$item) {
                $item['name']      = $users[$item['user_id']]['name'] ?? '账户已删除';
                $item['role_name'] = $roles[$users[$item['user_id']]['role_id']]['name'] ?? '角色已删除';
                $item['role_root'] = $roles[$users[$item['user_id']]['role_id']]['root'] == 0 ? '普通' : '超管';
            }
            $response = [
                'count' => $count,
                'items' => $items
            ];
            return $this->response($response);
        } else {
            $users = $this->service->getUserList();
            View::assign('users', $users);
            return View::fetch();
        }

    }


    /**
     * 删除日志
     */
    public function delete()
    {
        $filter = [
            ['log_id', 'in', $this->request->post('logIds')]
        ];
        $this->service->delHandleLog($filter);
        return $this->response();
    }


}
