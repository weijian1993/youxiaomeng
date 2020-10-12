<?php

namespace app\controller\admin\admin;

use app\consts\SystemConst;
use app\controller\admin\Common;
use think\facade\View;

/**
 * Class RoleController
 * @package app\controller\admin\admin
 * @property \app\service\AdminService $service
 */
class Role extends Common
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
                if (strlen($SO['root']) != 0) {
                    $filter[] = [
                        'root', '=', $SO['root']
                    ];
                }
            }
            $orderby  = [
                'root'    => 'desc',
                'role_id' => 'desc'
            ];
            $fields   = [
                'role_id', 'name', 'root', 'dateline'
            ];
            $page     = $this->request->param('page', 1);
            $limit    = $this->request->param('limit', SystemConst::DEFAULT_LIMIT);
            $items    = $this->service->getRoleList($filter, $fields, $orderby, $page, $limit);
            $count    = $this->service->getRoleCount($filter);
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
     * 添加角色
     * @return mixed
     */
    public function add()
    {
        if ($this->request->isAjax()) {
            $data = [
                'name' => $this->request->post('name', ''),
                'root' => $this->request->post('root', 0)
            ];
            $this->service->addRole($data);
            return $this->response();
        } else {
            return View::fetch();
        }
    }

    /**
     * 修改角色
     * @param $roleId
     * @return mixed
     */
    public function edit($roleId)
    {
        if ($this->request->isAjax()) {
            $filter = [
                'role_id' => $roleId
            ];
            $data   = [
                'name' => $this->request->post('name', ''),
                'root' => $this->request->post('root', 0)
            ];
            $this->service->setRole($filter, $data);
            return $this->response();
        } else {
            $filter = [
                'role_id' => $roleId
            ];
            $detail = $this->service->getRole($filter);
            View::assign('detail', $detail);
            return View::fetch();
        }
    }

    /**
     * 角色配置
     * @param $roleId
     * @return mixed
     */
    public function priv($roleId)
    {
        if ($this->request->isAjax()) {
            $filter  = [
                'role_id' => $roleId
            ];
            $fields  = ['role_id', 'priv'];
            $detail  = $this->service->getRole($filter, $fields);
            $orderby = [
                'orderby' => 'asc',
                'auth_id' => 'asc'
            ];
            $auths   = $this->service->getAuthList([], [], $orderby);
            foreach ($auths as &$auth) {
                $auth['LAY_CHECKED'] = 0;
                if (in_array($auth['auth_id'], $detail['priv'])) {
                    $auth['LAY_CHECKED'] = 1;
                }
            }
            return $this->response($auths);
        } else {
            $filter = [
                'role_id' => $roleId
            ];
            $fields = ['role_id', 'priv'];
            $detail = $this->service->getRole($filter, $fields);
            View::assign('detail', $detail);
            return View::fetch();
        }
    }

    /**
     * 设置权限
     * @param $roleId
     * @return Role
     */
    public function setPriv($roleId)
    {
        $filter = [
            'role_id' => $roleId
        ];
        $data   = [
            'priv' => $this->request->post('priv', [])
        ];

        $this->service->setRole($filter, $data);
        return $this->response();
    }

    /**
     * 删除角色
     */
    public function delete()
    {
        $filter = [
            ['role_id', 'in', $this->request->post('roleIds')]
        ];
        $this->service->delRole($filter);
        return $this->response();
    }


}
