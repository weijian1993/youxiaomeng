<?php

namespace app\controller\admin\admin;

use app\consts\SystemConst;
use app\controller\admin\Common;
use \think\facade\View;


/**
 * Class AuthController
 * @package app\controller\admin\admin
 * @property \app\service\AdminService $service
 */
class Auth extends Common
{
    /**
     * 初始化
     */
    public function initialize()
    {
        $service       = app('adminService');
        $this->service = $service;
        parent::initialize();
    }

    /**
     * 首页
     * @return mixed
     */
    public function index()
    {

        if ($this->request->isAjax()) {

            $orderby = [
                'orderby' => 'asc',
                'auth_id' => 'asc'
            ];
            $auths   = $this->service->getAuthList([], [], $orderby);
            return  $this->response($auths);
        } else {
            return View::fetch();
        }
    }

    /**
     * 添加权限
     * @return mixed
     */
    public function add()
    {
        if ($this->request->isAjax()) {
            $data = [
                'pid'     => $this->request->post('pid', '0'),
                'title'   => $this->request->post('title'),
                'name'    => $this->request->post('name'),
                'path'    => $this->request->post('path'),
                'icon'    => $this->request->post('icon', ''),
                'type'    => $this->request->post('type'),
                'hidden'  => $this->request->post('hidden'),
                'record'  => $this->request->post('record'),
                'orderby' => $this->request->post('orderby')
            ];
            $this->service->addAuth($data);
            return $this->response();
        } else {
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
            $items   = formatSort($auths, 0, 1, ['pid' => 'pid', 'id' => 'auth_id']);
            foreach ($items as &$item) {
                $item['title'] = str_repeat("|--", $item['level']) . $item['title'];
            }
            View::assign('items', $items);
            return View::fetch();
        }
    }

    /**
     * 修改权限
     * @param $authId
     * @return mixed
     */
    public function edit($authId)
    {
        if ($this->request->isAjax()) {
            $filter = [
                'auth_id' => $authId
            ];
            $data   = [
                'pid'     => $this->request->post('pid', '0'),
                'title'   => $this->request->post('title'),
                'name'    => $this->request->post('name'),
                'path'    => $this->request->post('path'),
                'icon'    => $this->request->post('icon', ''),
                'type'    => $this->request->post('type'),
                'hidden'  => $this->request->post('hidden'),
                'record'  => $this->request->post('record'),
                'orderby' => $this->request->post('orderby')
            ];
            $this->service->setAuth($filter, $data);
            return $this->response();
        } else {
            $filter  = [
                'auth_id' => $authId
            ];
            $detail  = $this->service->getAuth($filter);
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
            $items   = formatSort($auths, 0, 1, ['pid' => 'pid', 'id' => 'auth_id']);
            foreach ($items as &$item) {
                $item['title'] = str_repeat("|--", $item['level']) . $item['title'];
            }
            View::assign('items', $items);
            View::assign('detail', $detail);
            return View::fetch();
        }
    }

    /**
     * 删除
     */
    public function delete()
    {
        $filter = [
            ['auth_id', 'in', $this->request->post('authIds')]
        ];
        $this->service->delAuth($filter);
        return $this->response();
    }


}
