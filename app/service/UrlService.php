<?php

namespace app\service;

use app\models\UrlCateModel;
use app\models\UrlLogModel;
use app\models\UrlModel;
use think\Service;

class UrlService extends Service
{
    /*---------------------------------网站分类-------------------------------------*/

    /**
     * 获取网站分类
     * @param array $filter
     * @param array $fields
     * @param array $orderby
     * @param int   $page
     * @param int   $limit
     * @return array
     */
    public function getCateList($filter = [], $fields = [], $orderby = [], $page = 0, $limit = 0)
    {
        try {
            $query = UrlCateModel::where($filter)
                ->field($fields)
                ->order($orderby);

            if ($page && $limit) {
                $limit = (int)$limit;
                $query->limit(($page - 1) * $limit, $limit);
            }
            $result = $query->select();
            return empty($result) ? [] : $result->toArray();
        } catch (\Throwable $e) {
            throw new \RuntimeException('获取网址分类失败');
        }
    }

    /**
     * 获取网站分类
     * @param $filter
     * @return float|string
     */
    public function getCateCount($filter = [])
    {
        try {
            $result = UrlCateModel::where($filter)->count();
            return $result;
        } catch (\Throwable $e) {

            throw new \RuntimeException('获取网址分类数量失败');
        }
    }

    /**
     * 获取单条网址分类
     * @param $filter
     * @param $fields
     * @return array
     */
    public function getCate($filter, $fields = [])
    {
        if (empty($filter)) {
            throw new \RuntimeException('查询条件不可为空');
        }
        try {
            $result = UrlCateModel::where($filter)->field($fields)->find();
            return empty($result) ? [] : $result->toArray();
        } catch (\Throwable $e) {
            throw new \RuntimeException('获取网址分类失败');
        }
    }


    /**
     * 添加网站分类
     * @param $data
     * @return string
     */
    public function addCate($data)
    {

        if (!$data['title']) {
            throw new \RuntimeException('分类名称不能为空');
        }
        if (!$data['orderby'] or !is_numeric($data['orderby'])) {
            throw new \RuntimeException('排序字段不合法');
        }
        try {
            $data['dateline'] = time();
            $data             = UrlCateModel::create($data);
            return $data;
        } catch (\Throwable $e) {
            throw new \RuntimeException('网站分类添加失败');
        }
    }


    /**
     * 删除分类
     * @param $filter
     * @return bool
     */
    public function delCate($filter)
    {
        try {
            if (empty($filter)) {
                throw new \RuntimeException('未指定删除条件');
            }
            UrlCateModel::where($filter)->delete();
            return true;
        } catch (\Throwable $e) {
            throw new \RuntimeException('删除分类失败');
        }
    }


    /**
     * 修改分类
     * @param $filter
     * @param $data
     * @return int|string
     */
    public function setCate($filter, $data)
    {
        if (!$data['title']) {
            throw new \RuntimeException('分类名称不能为空');
        }
        if (!$data['orderby'] or !is_numeric($data['orderby'])) {
            throw new \RuntimeException('排序字段不合法');
        }
        try {
            UrlCateModel::where($filter)->update($data);
            return true;
        } catch (\Throwable $e) {
            throw new \RuntimeException('分类修改失败');
        }
    }

    /**
     * 获取网站列表
     * @param array $filter
     * @param array $fields
     * @param array $orderby
     * @param int   $page
     * @param int   $limit
     * @return array
     */
    public function getUrlList($filter = [], $fields = [], $orderby = [], $page = 0, $limit = 0)
    {
        try {
            $query = UrlModel::where($filter)
                ->field($fields)
                ->order($orderby);

            if ($page && $limit) {
                $query->limit(($page - 1) * $limit, $limit);
            }
            $result = $query->select();
            return empty($result) ? [] : $result->toArray();
        } catch (\Throwable $e) {

            throw new \RuntimeException('获取网址失败');
        }
    }

    /**
     * 获取网站数量
     * @param $filter
     * @return float|string
     */
    public function getUrlCount($filter = [])
    {
        try {
            $result = UrlModel::where($filter)->count();
            return $result;
        } catch (\Throwable $e) {
            throw new \RuntimeException('获取网址数量失败');
        }
    }

    /**
     * 获取单条网址
     * @param $filter
     * @param $fields
     * @return array
     */
    public function getUrl($filter, $fields = [])
    {
        if (empty($filter)) {
            throw new \RuntimeException('查询条件不可为空');
        }
        try {
            $result = UrlModel::where($filter)->field($fields)->find();
            return empty($result) ? [] : $result->toArray();
        } catch (\Throwable $e) {
            throw new \RuntimeException('获取网址失败');
        }
    }

    /**
     * 网站内容修改
     * @param $filter
     * @param $data
     * @return bool
     */
    public function setUrl($filter, $data)
    {
        try {
            if (empty($filter)) {
                throw new \RuntimeException('要修改的网站不存在');
            }
            UrlModel::where($filter)->update($data);
            return true;
        } catch (\Throwable $e) {
            throw new \RuntimeException('网站内容修改失败');
        }
    }

    /**
     * 添加网站
     * @param $data
     * @return string
     */
    public function addUrl($data)
    {
        if (!$data['title']) {
            throw new \RuntimeException('网站名称不可为空');
        } else if (!$data['orderby'] or !is_numeric($data['orderby'])) {
            throw new \RuntimeException('排序字段不合法');
        } else if (!$data['cate_id'] or !is_numeric($data['cate_id'])) {
            throw new \RuntimeException('网站分类不能为空');
        } else if (!$data['url'] or !CheckUrl($data['url'])) {
            throw new \RuntimeException('网址不合法');
        }
        try {
            $data['dateline'] = time();
            $data             = UrlModel::create($data);
            return $data;
        } catch (\Throwable $e) {
            throw new \RuntimeException('网站添加失败');
        }
    }


    /**
     * 删除网站
     * @param $filter
     * @return bool
     */
    public function delUrl($filter)
    {
        try {
            if (empty($filter)) {
                throw new \RuntimeException('未指定删除条件');
            }
            UrlModel::where($filter)->delete();
            return true;
        } catch (\Throwable $e) {
            throw new \RuntimeException('删除网站失败');
        }
    }

    /**
     * 获取网站错误日志列表
     * @param array $filter
     * @param array $fields
     * @param array $orderby
     * @param int   $page
     * @param int   $limit
     * @return array
     */
    public function getLogList($filter = [], $fields = [], $orderby = [], $page = 0, $limit = 0)
    {
        try {
            $query = UrlLogModel::where($filter)
                ->field($fields)
                ->order($orderby);
            if ($page && $limit) {
                $limit = (int)$limit;
                $query->limit(($page - 1) * $limit, $limit);
            }
            $result = $query->select();
            return empty($result) ? [] : $result->toArray();
        } catch (\Throwable $e) {
            throw new \RuntimeException('获取网站错误日志列表失败');
        }
    }

    /**
     * 获取网站错误日志数量
     * @param $filter
     * @return float|string
     */
    public function getLogCount($filter = [])
    {
        try {
            $result = UrlLogModel::where($filter)->count();
            return $result;
        } catch (\Throwable $e) {
            throw new \RuntimeException('获取网站错误日志数量失败');
        }
    }

    /**
     * 添加网站错误日志
     * @param $data
     * @return mixed
     */
    public function addLog($data)
    {
        try {
            $data = UrlLogModel::create($data);
            return $data;
        } catch (\Throwable $e) {
            throw new \RuntimeException('网站错误日志添加失败');
        }
    }

    /**
     * 删除网站错误日志
     * @param $filter
     * @return bool
     */
    public function delLoginLog($filter)
    {
        try {
            if (empty($filter)) {
                throw new \RuntimeException('未指定删除条件');
            }
            UrlLogModel::where($filter)->delete();
            return true;
        } catch (\Throwable $e) {
            throw new \RuntimeException('删除网站错误日志失败');
        }
    }

    /**
     * 批量添加网站错误日志
     * @param $data
     * @return bool|\think\Collection
     */
    public function batchUrlLog($data)
    {
        try {
            if (empty($data)) {
                return false;
            }
            $msgModel = new UrlLogModel();
            $data     = $msgModel->saveAll($data);
            return $data;
        } catch (\Throwable $e) {
            throw new \RuntimeException('批量写入网站日志失败');
        }
    }

    /**
     * 钉钉发送文字请求方法
     * @param $post_string
     * @return bool|string
     */
    function request_by_curl($post_string)
    {
        $remote_server = "https://oapi.dingtalk.com/robot/send?access_token=7fbeefb962dfde904cd5f34a6a3378a9b9129e0913d33a7ada2bde21a65641c2";
        $ch            = curl_init();
        curl_setopt($ch, CURLOPT_URL, $remote_server);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json;charset=utf-8'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // 不用开启curl证书验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

}