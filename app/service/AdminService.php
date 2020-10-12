<?php
declare (strict_types=1);

namespace app\service;

use app\consts\ErrorCodeConst;
use app\consts\SystemConst;
use app\models\AdminAuthModel;
use app\models\AdminHandleLogModel;
use app\models\AdminLoginLogModel;
use app\models\AdminRoleModel;
use app\models\AdminUserModel;
use think\Service;

/**
 * 应用服务类
 */
class AdminService extends Service
{
    /**
     * 权限检测
     * @param $userId
     * @param $path
     * @return bool
     */
    public function checkRole($userId, $path)
    {
        $path = trim($path, '.html');
        if ($path == 'admin.index/index' || $path == 'admin.index/home') {
            return true;
        }
        if (!$user = $this->getUser(['user_id' => $userId])) {
            throw new \RuntimeException('账号不存在', ErrorCodeConst::NOT_LOGIN);
        } else if (!$role = $this->getRole(['role_id' => $user['role_id']])) {
            throw new \RuntimeException('账号角色不存在', ErrorCodeConst::NOT_LOGIN);
        } else if ($role['root'] != SystemConst::ROLE_ROOT_ON && ($path != 'admin.index/index' || $path != 'admin.index/home')) {
            $filter = [
                ['auth_id', 'in', $role['priv']]
            ];
            $auths  = $this->getAuthList($filter);
            $pass   = false;

            foreach ($auths as $v) {
                $systemPath = trim($v['path'], '/');
                if ($systemPath == substr($path, 0, strlen($systemPath)) && !empty($v['path'])) {
                    $pass = true;
                    break;
                }
            }
            if ($pass == false) {
                throw new \RuntimeException("无权限访问");
            }
            return true;
        } else {
            return true;
        }
    }


    /**
     * 获取管理员角色列表
     * @param array $filter
     * @param array $fields
     * @param array $orderby
     * @param int   $page
     * @param int   $limit
     * @return array
     */
    public function getRoleList($filter = [], $fields = [], $orderby = [], $page = 0, $limit = 0)
    {
        try {
            $query = AdminRoleModel::where($filter)
                ->field($fields)
                ->order($orderby);
            if ($page && $limit) {
                $limit = (int)$limit;
                $query->limit(($page - 1) * $limit, $limit);
            }
            $result = $query->select();
            return empty($result) ? [] : $result->toArray();
        } catch (\Throwable $e) {

            throw new \RuntimeException('获取管理员角色列表失败');
        }
    }

    /**
     * 获取管理员角色数量
     * @param $filter
     * @return float|string
     */
    public function getRoleCount($filter = [])
    {
        try {
            $result = AdminRoleModel::where($filter)->count();
            return $result;
        } catch (\Throwable $e) {
            throw new \RuntimeException('获取管理员角色数量失败');
        }
    }

    /**
     * 获取管理员
     * @param $filter
     * @param $fields
     * @return array
     */
    public function getRole($filter, $fields = [])
    {
        if (empty($filter)) {
            throw new \RuntimeException('查询条件不可为空');
        }
        try {
            $result = AdminRoleModel::where($filter)->field($fields)->find();
            return empty($result) ? [] : $result->toArray();
        } catch (\Throwable $e) {
            throw new \RuntimeException('获取管理员信息失败');
        }
    }

    /**
     * 添加管理员角色
     * @param $data
     * @return string
     */
    public function addRole($data)
    {
        if (!$data['name']) {
            throw new \RuntimeException('角色名称不可为空');
        }
        if (!in_array($data['root'], [0, 1])) {
            throw new \RuntimeException('角色类型不合法');
        }
        try {
            $data['dateline'] = time();
            $data             = AdminRoleModel::create($data);
            return $data;
        } catch (\Throwable $e) {
            throw new \RuntimeException('管理员角色添加失败');
        }
    }

    /**
     * 修改管理员角色
     * @param $filter
     * @param $data
     * @return string
     */
    public function setRole($filter, $data)
    {
        if (!$this->getRole($filter)) {
            throw new \RuntimeException('角色权限不合法');
        }
        try {
            if (isset($data['priv']) && !empty($data['priv'])) {
                $data['priv'] = implode(',', $data['priv']);
            } else {
                $data['priv'] = '';
            }

            AdminRoleModel::where($filter)->update($data);
            return true;
        } catch (\Throwable $e) {
            throw new \RuntimeException('管理员角色修改失败');
        }
    }

    /**
     * 删除管理员角色
     * @param $filter
     * @return bool
     */
    public function delRole($filter)
    {
        try {
            if (empty($filter)) {
                throw new \RuntimeException('未指定删除条件');
            }
            AdminRoleModel::where($filter)->delete();
            return true;
        } catch (\Throwable $e) {
            throw new \RuntimeException('删除管理员角色失败');
        }
    }


    /**
     * 获取权限列表
     * @param array $filter
     * @param array $fields
     * @param array $orderby
     * @param int   $page
     * @param int   $limit
     * @return array
     */
    public function getAuthList($filter = [], $fields = [], $orderby = [], $page = 0, $limit = 0)
    {
        try {
            $query = AdminAuthModel::where($filter)
                ->field($fields)
                ->order($orderby);
            if ($page && $limit) {
                $limit = (int)$limit;
                $query->limit(($page - 1) * $limit, $limit);
            }
            $result = $query->select();
            return empty($result) ? [] : $result->toArray();
        } catch (\Throwable $e) {
            throw new \RuntimeException('获取权限列表失败');
        }
    }


    /**
     * 获取单条管理员
     * @param       $filter
     * @param array $fields
     * @return array
     */
    public function getUser($filter, $fields = [])
    {
        if (empty($filter)) {
            throw new \RuntimeException('查询条件不可为空');
        }
        try {
            if (isset($filter['passwd'])) {
                $filter['passwd'] = md5($filter['passwd']);
            }
            $result = AdminUserModel::where($filter)->field($fields)->find();
            return empty($result) ? [] : $result->toArray();
        } catch (\Throwable $e) {
            throw new \RuntimeException('获取管理员信息失败');
        }
    }

    /**
     * 添加用户登陆日志
     * @param $data
     * @return mixed
     */
    public function addLoginLog($data)
    {
        try {
            $data = AdminLoginLogModel::create($data);
            return $data;
        } catch (\Throwable $e) {

            throw new \RuntimeException('用户登陆日志添加失败');
        }
    }


    /**
     * 获取权限
     * @param $filter
     * @param $fields
     * @return array
     */
    public function getAuth($filter, $fields = [])
    {
        if (empty($filter)) {
            throw new \RuntimeException('查询条件不可为空');
        }
        try {
            $result = AdminAuthModel::where($filter)->field($fields)->find();
            return empty($result) ? [] : $result->toArray();
        } catch (\Throwable $e) {
            throw new \RuntimeException('获取权限信息失败');
        }
    }

    /**
     * 添加权限
     * @param $data
     * @return string
     */
    public function addAuth($data)
    {
        if (!$data['name']) {
            throw new \RuntimeException('权限名称不可为空');
        } else if (!$data['title']) {
            throw new \RuntimeException('权限标识不可为空');
        } else if (!in_array($data['type'], ['menu', 'button']) or !$data['type']) {
            throw new \RuntimeException('权限类型只能是菜单或按钮');
        } else if (!in_array($data['hidden'], [0, 1])) {
            throw new \RuntimeException('权限隐藏显示不合法');
        } else if (!$data['orderby'] or !is_numeric($data['orderby'])) {
            throw new \RuntimeException('排序字段不合法');
        }
        $parent = $this->getAuth(['auth_id' => $data['pid']], ['level']);
        if ($parent && $data['type'] == SystemConst::AUTH_TYPE_MENU && $parent['level'] >= 3) {
            throw new \RuntimeException('菜单权限最多只可嵌套三层');
        }
        $parent['level']  = $parent['level'] ?? 0;
        $data['dateline'] = time();

        $data['path']  = strtolower($data['path']);
        $data['level'] = isset($parent) ? $parent['level'] + 1 : 1;
        try {
            $data = AdminAuthModel::create($data);
            return $data;
        } catch (\Throwable $e) {
            throw new \RuntimeException('权限添加失败');
        }
    }

    /**
     * 修改权限
     * @param $filter
     * @param $data
     * @return int|string
     */
    public function setAuth($filter, $data)
    {
        if (!$data['name']) {
            throw new \RuntimeException('权限名称不可为空');
        } else if (!$data['title']) {
            throw new \RuntimeException('权限标识不可为空');
        } else if (!in_array($data['type'], ['menu', 'button']) or !$data['type']) {
            throw new \RuntimeException('权限类型只能是菜单或按钮');
        } else if (!in_array($data['hidden'], [0, 1])) {
            throw new \RuntimeException('权限隐藏显示不合法');
        } else if (!$data['orderby'] or !is_numeric($data['orderby'])) {
            throw new \RuntimeException('排序字段不合法');
        }
        $parent = $this->getAuth(['auth_id' => $data['pid']], ['level']);
        if (!$this->getAuth($filter)) {
            throw new \RuntimeException('该权限不存在');
        } else if ($parent && $data['type'] == SystemConst::AUTH_TYPE_MENU && $parent['level'] >= 3) {
            throw new \RuntimeException('菜单权限最多只可嵌套三层');
        }
        $parent['level'] = $parent['level'] ?? 0;
        try {
            $data['path']  = strtolower($data['path']);
            $data['level'] = isset($parent) ? $parent['level'] + 1 : 1;
            AdminAuthModel::where($filter)->update($data);
            return true;
        } catch (\Throwable $e) {
            throw new \RuntimeException('权限修改失败');
        }
    }

    /**
     * 删除权限
     * @param $filter
     * @return bool
     */
    public function delAuth($filter)
    {
        try {
            if (empty($filter)) {
                throw new \RuntimeException('未指定删除条件');
            }
            AdminAuthModel::where($filter)->delete();
            return true;
        } catch (\Throwable $e) {
            throw new \RuntimeException('删除权限失败');
        }
    }

    /**
     * 添加管理员
     * @param $data
     * @return string
     */
    public function addUser($data)
    {
        if (!$data['name']) {
            throw new \RuntimeException('管理员名称不可为空');
        } else if (!ctype_alnum($data['name'])) {
            throw new \RuntimeException('管理员名称只能为字母和数字');
        } else if (strlen($data['name']) > 15) {
            throw new \RuntimeException('管理员名称长度不可超过十五位');
        } else if (!ctype_alnum($data['passwd'])) {
            throw new \RuntimeException('密码只能为字母和数字');
        } else if ($data['passwd'] != $data['repasswd']) {
            throw new \RuntimeException('两次密码不一致');
        } else if (!$data['role_id']) {
            throw new \RuntimeException('账号角色类型不能为空');
        } else if (!$data['role_id'] < 0 or !is_numeric($data['role_id'])) {
            throw new \RuntimeException('账号角色类型错误');
        }
        if ($this->getUser(['name' => $data['name']])) {
            throw new \RuntimeException('账号名称已存在');
        }
        try {
            if (isset($data['repasswd'])) unset($data['repasswd']);
            if (isset($data['passwd'])) $data['passwd'] = md5($data['passwd']);
            $data['dateline'] = time();
            $data['closed']   = SystemConst::CLOSED_OFF;
            $data             = AdminUserModel::create($data);
            return $data;
        } catch (\Throwable $e) {
            throw new \RuntimeException('管理员添加失败');
        }
    }

    /**
     * 修改管理员
     * @param $filter
     * @param $data
     * @return int|string
     */
    public function setUser($filter, $data)
    {

        if (!$this->getUser($filter)) {
            throw new \RuntimeException('该管理员不存在');
        }
        try {
            if (isset($data['repasswd'])) unset($data['repasswd']);
            if (isset($data['passwd'])) $data['passwd'] = md5($data['passwd']);
            AdminUserModel::where($filter)->update($data);
            return true;
        } catch (\Throwable $e) {
            throw new \RuntimeException('管理员修改失败');
        }
    }


    /**
     * 获取管理员列表
     * @param array $filter
     * @param array $fields
     * @param array $orderby
     * @param int   $page
     * @param int   $limit
     * @return array
     */
    public function getUserList($filter = [], $fields = [], $orderby = [], $page = 0, $limit = 0)
    {
        try {
            $query = AdminUserModel::where($filter)
                ->field($fields)
                ->order($orderby);
            if ($page && $limit) {
                $limit = (int)$limit;
                $query->limit(($page - 1) * $limit, $limit);
            }
            $result = $query->select();
            return empty($result) ? [] : $result->toArray();
        } catch (\Throwable $e) {
            throw new \RuntimeException('获取管理员列表失败');
        }
    }

    /**
     * 获取管理员数量
     * @param $filter
     * @return float|string
     */
    public function getUserCount($filter = [])
    {
        try {
            $result = AdminUserModel::where($filter)->count();
            return $result;
        } catch (\Throwable $e) {
            throw new \RuntimeException('获取管理员数量失败');
        }
    }

    /**
     * 删除管理员
     * @param $filter
     * @return bool
     */
    public function delUser($filter)
    {
        try {
            if (empty($filter)) {
                throw new \RuntimeException('未指定删除条件');
            }
            AdminUserModel::where($filter)->delete();
            return true;
        } catch (\Throwable $e) {
            throw new \RuntimeException('删除管理员失败');
        }
    }

    /**
     * 获取管理员登陆日志列表
     * @param array $filter
     * @param array $fields
     * @param array $orderby
     * @param int   $page
     * @param int   $limit
     * @return array
     */
    public function getLoginLogList($filter = [], $fields = [], $orderby = [], $page = 0, $limit = 0)
    {
        try {
            $query = AdminLoginLogModel::where($filter)
                ->field($fields)
                ->order($orderby);
            if ($page && $limit) {
                $limit = (int)$limit;
                $query->limit(($page - 1) * $limit, $limit);
            }
            $result = $query->select();
            return empty($result) ? [] : $result->toArray();
        } catch (\Throwable $e) {
            throw new \RuntimeException('获取管理员登陆日志列表失败');
        }
    }

    /**
     * 获取管理员登陆日志数量
     * @param $filter
     * @return float|string
     */
    public function getLoginLogCount($filter = [])
    {
        try {
            $result = AdminLoginLogModel::where($filter)->count();
            return $result;
        } catch (\Throwable $e) {
            throw new \RuntimeException('获取管理员登陆日志数量失败');
        }
    }


    /**
     * 删除登陆日志
     * @param $filter
     * @return bool
     */
    public function delLoginLog($filter)
    {
        try {
            if (empty($filter)) {
                throw new \RuntimeException('未指定删除条件');
            }
            AdminLoginLogModel::where($filter)->delete();
            return true;
        } catch (\Throwable $e) {
            throw new \RuntimeException('删除登陆日志失败');
        }
    }


    /**
     * 获取操作日志列表
     * @param array $filter
     * @param array $fields
     * @param array $orderby
     * @param int   $page
     * @param int   $limit
     * @return array
     */
    public function getHandleLogList($filter = [], $fields = [], $orderby = [], $page = 0, $limit = 0)
    {
        try {
            $query = AdminHandleLogModel::where($filter)
                ->field($fields)
                ->order($orderby);
            if ($page && $limit) {
                $limit = (int)$limit;
                $query->limit(($page - 1) * $limit, $limit);
            }
            $result = $query->select();
            return empty($result) ? [] : $result->toArray();
        } catch (\Throwable $e) {
            throw new \RuntimeException('获取操作日志列表失败');
        }
    }

    /**
     * 获取操作日志数量
     * @param $filter
     * @return float|string
     */
    public function getHandleLogCount($filter = [])
    {
        try {
            $result = AdminHandleLogModel::where($filter)->count();
            return $result;
        } catch (\Throwable $e) {
            throw new \RuntimeException('获取操作日志数量失败');
        }
    }

    /**
     * 添加操作日志
     * @param $data
     * @return mixed
     */
    public function addHandleLog($data)
    {
        try {
            $data = AdminHandleLogModel::create($data);
            return $data;
        } catch (\Throwable $e) {
            throw new \RuntimeException('操作日志添加失败');
        }
    }

    /**
     * 删除操作日志
     * @param $filter
     * @return bool
     */
    public function delHandleLog($filter)
    {
        try {
            if (empty($filter)) {
                throw new \RuntimeException('未指定删除条件');
            }
            AdminHandleLogModel::where($filter)->delete();
            return true;
        } catch (\Throwable $e) {
            throw new \RuntimeException('删除操作日志失败');
        }
    }
}
