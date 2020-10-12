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
class User extends Common
{

    public function initialize()
    {
        parent::initialize();
        $service       = app('adminService');
        $this->service = $service;
    }

    /**
     * 初始化页面
     * @return mixed
     */
    public function index()
    {
        if ($this->request->isAjax()) {
            $filter = [];
            if ($SO = $this->request->param('SO')) {
                if ($SO['name']) {
                    $filter[] = [
                        'name', 'like', '%' . trim($SO['name']) . '%'
                    ];
                }
            }
            $orderby = [
                'user_id' => 'desc'
            ];
            $page    = $this->request->param('page', 1);
            $limit   = $this->request->param('limit', SystemConst::DEFAULT_LIMIT);
            $items   = $this->service->getUserList($filter, [], $orderby, $page, $limit);
            $count   = $this->service->getUserCount($filter);
            $filter  = [
                ['role_id', 'in', array_column($items, 'role_id')]
            ];
            $roles   = $this->service->getRoleList($filter);
            $roles   = array_column($roles, null, 'role_id');
            foreach ($items as &$item) {
                $item['role_name'] = $roles[$item['role_id']]['name'] ?? '角色不存在';
                $item['root']      = $roles[$item['role_id']]['root'] ?? SystemConst::ROLE_ROOT_OFF;
            }
            $response = [
                'count' => $count,
                'items' => $items
            ];
            return $this->response($response);
        } else {
            return View::fetch();
        }

    }

    /**
     * 添加账号
     * @return mixed
     */
    public function add()
    {
        if ($this->request->isAjax()) {
            $data = [
                'name'     => $this->request->post('name', ''),
                'role_id'  => $this->request->post('role_id', 0),
                'passwd'   => $this->request->post('passwd', 0),
                'repasswd' => $this->request->post('repasswd', 0),
            ];
            $this->service->addUser($data);
            return $this->response();
        } else {
            $roles = $this->service->getRoleList();
            View::assign('roles', $roles);
            return View::fetch();
        }
    }

    /**
     * 修改账号
     * @return mixed
     */
    public function edit()
    {
        if ($this->request->isAjax()) {
            $filter = [
                'user_id' => $this->request->get('user_id')
            ];
            $data   = [
                'name'    => $this->request->post('name', ''),
                'role_id' => $this->request->post('role_id', 0),
            ];
            $this->service->setUser($filter, $data);
            return $this->response();
        } else {
            $filter = [
                'user_id' => $this->request->get('userId')
            ];
            $detail = $this->service->getUser($filter);
            $roles  = $this->service->getRoleList();
            View::assign('roles', $roles);
            View::assign('detail', $detail);
            return View::fetch();
        }
    }

    /**
     * 重置密码
     */
    public function reset()
    {
        $filter = [
            'user_id' => $this->request->get('user_id')
        ];
        $data   = [
            'passwd' => $this->request->post('passwd', ''),
        ];
        $this->service->setUser($filter, $data);
        return $this->response();
    }


    /**
     * 删除账号
     */
    public function delete()
    {
        $userIds = $this->request->post('userIds');
        foreach ($userIds as $userId) {
            $this->service->delUser(['user_id' => $userId]);
        }
        return $this->response();
    }

    /**
     * 基本信息
     * @return mixed
     */
    public function info()
    {
        $filter = [
            'user_id' => $this->request->userId
        ];
        $user   = $this->service->getUser($filter);
        $roles  = $this->service->getRoleList([], ['role_id', 'name']);
        View::assign('roles', $roles);
        View::assign('user', $user);
        return View::fetch();
    }

    /**
     * 基本信息
     * @return mixed
     */
    public function passwd()
    {
        $filter = [
            'user_id' => $this->request->userId
        ];
        $user   = $this->service->getUser($filter);
        if ($this->request->isAjax()) {
            if (!$old = $this->request->post('old')) {
                throw new \RuntimeException('请输入旧密码');
            } else if (md5($old) != $user['passwd']) {
                throw new \RuntimeException('密码输入错误');
            } else if (!$new = $this->request->post('new')) {
                throw new \RuntimeException('请输入新密码');
            } else if (!$renew = $this->request->post('renew')) {
                throw new \RuntimeException('请重复输入新密码');
            } else if ($new != $renew) {
                throw new \RuntimeException('俩次密码不一致');
            }
            $this->service->setUser($filter, [
                'passwd' => $new
            ]);
          return  $this->response();
        } else {
            $roles = $this->service->getRoleList([], ['role_id', 'name']);
            View::assign('roles', $roles);
            View::assign('user', $user);
            return View::fetch();
        }
    }


}
