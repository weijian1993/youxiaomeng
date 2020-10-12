<?php

namespace app\controller\admin\URL;

use app\consts\SystemConst;
use app\controller\admin\Common;
use think\facade\View;

/**
 * Class CateController
 * @package app\controller\admin\admin
 * @property \app\service\UrlService $service
 */
class Url extends Common
{
    /**
     * 初始化页面
     */
    public function initialize()
    {
        $service       = app('urlService');
        $this->service = $service;
        parent::initialize();
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
                if (!empty($SO['title'])) {
                    $filter[] = [
                        'title', 'like', '%' . trim($SO['title']) . '%'
                    ];
                }
                if (!empty($SO['url'])) {
                    $filter[] = [
                        'url', 'like', '%' . trim($SO['url']) . '%'
                    ];
                }
                if (is_numeric($SO['closed'])) {
                    $filter[] = [
                        'closed', '=', $SO['closed']
                    ];
                }
                if (is_numeric($SO['status'])) {
                    $filter[] = [
                        'status', '=', $SO['status']
                    ];
                }
                if (is_numeric($SO['cate_id'])) {
                    $filter[] = [
                        'cate_id', '=', $SO['cate_id']
                    ];
                }
                if (!empty($SO['dateline'])) {
                    $times    = explode('-', $SO['dateline']);
                    $stime    = strtotime(str_replace('/', '-', $times[0]));
                    $ltime    = strtotime(str_replace('/', '-', $times[1])) + 86399;
                    $filter[] = [
                        'dateline', 'between', [$stime, $ltime]
                    ];
                }
            }
            $orderby = [
                'orderby' => 'asc'
            ];
            $page    = $this->request->param('page', 1);
            $limit   = $this->request->param('limit', SystemConst::DEFAULT_LIMIT);

            $count   = $this->service->getUrlCount($filter);
            $items   = $this->service->getUrlList($filter, [], $orderby, $page, $limit);
            $cateIds = [];
            foreach ($items as $v) {
                $cateIds[] = $v['cate_id'];
            }
            $cates = $this->service->getCateList(['cate_id' => $cateIds], ['cate_id', 'title']);
            $cates = array_column($cates, null, 'cate_id');

            foreach ($items as &$item) {
                $item['cate'] = $cates[$item['cate_id']]['title']??'分类不存在';
            }
            $response = [
                'count' => $count,
                'items' => $items
            ];
            return $this->response($response);
        } else {
            $cates = [];
            $cates = $this->service->getCateList([], [], ['orderby' => 'asc', 'cate_id' => 'asc']);
            View::assign('cates', $cates);
            return View::fetch();
        }

    }


    /**
     * 关闭检测
     * @param $urlId
     * @return Url
     */
    public function close($urlId)
    {
        if (!$item = $this->service->getUrl(['url_id' => $urlId])) {
            throw new \RuntimeException('禁止检测的网址不存在');
        }
        $data   = [
            'closed' => SystemConst::CLOSED_ON
        ];
        $filter = [
            'url_id' => $urlId
        ];
        $this->service->setUrl($filter, $data);
        return $this->response();
    }

    /**
     * 开启检测
     * @param $urlId
     * @return Url
     */
    public function open($urlId)
    {
        if (!$item = $this->service->getUrl(['url_id' => $urlId])) {
            throw new \RuntimeException('开启字符检测的网址不存在');
        }
        $data   = [
            'closed' => SystemConst::CLOSED_OFF
        ];
        $filter = [
            'url_id' => $urlId
        ];
        $this->service->setUrl($filter, $data);
        return $this->response();
    }


    /**
     * 添加网址
     * @return mixed
     */
    public function add()
    {
        if ($this->request->isAjax()) {
            $data = [
                'title'   => $this->request->post('title', ''),//名称
                'cate_id' => $this->request->post('cate_id', ''),//分类id,
                'url'     => $this->request->post('url', ''),//网址地址
                'orderby' => $this->request->post('orderby'),
                'status'  => $this->request->post('status', 1),
            ];
            if ($data['status'] == 0) {
                $special = $this->request->post('special', []);
                if (empty($special)) {
                    throw new  \RuntimeException('特殊字符不能为空');
                }
                $data['special'] = serialize($special);
            }

            $this->service->addUrl($data);
            return $this->response();
        } else {
            $cates = [];
            $cates = $this->service->getCateList([], [], ['orderby' => 'asc', 'cate_id' => 'asc']);
            View::assign('cates', $cates);
            return View::fetch();
        }
    }


    /**
     * 修改网站
     * @param $urlId
     * @return mixed
     */
    public function edit($urlId)
    {
        if (!$detail = $this->service->getUrl(['url_id' => $urlId])) {
            throw new  \RuntimeException('修改的数据不存在');
        } else if ($this->request->isAjax()) {
            $filter = [
                'url_id' => $urlId
            ];
            $data   = [
                'title'   => $this->request->post('title', ''),//名称
                'cate_id' => $this->request->post('cate_id', ''),//分类id,
                'url'     => $this->request->post('url', ''),//网址地址
                'orderby' => $this->request->post('orderby'),
                'status'  => $this->request->post('status', 1),
            ];
            if ($data['status'] == 0) {
                $special = $this->request->post('special', []);
                if (empty($special)) {
                    throw new  \RuntimeException('特殊字符不能为空');
                }
                $data['special'] = serialize($special);
            }
            $this->service->setUrl($filter, $data);
            return $this->response();
        } else {
            $cates = [];
            $cates = $this->service->getCateList([], [], ['orderby' => 'asc', 'cate_id' => 'asc']);
            View::assign('cates', $cates);
            $detail['special'] = unserialize($detail['special']) ? unserialize($detail['special']) : [];
            View::assign('detail', $detail);
            return View::fetch();
        }
    }


    /**
     * 删除分类
     */
    public function delete()
    {
        $urlIds = $this->request->post('urlIds');
        foreach ($urlIds as $urlId) {
            $this->service->delUrl(['url_id' => $urlId]);
        }
        return $this->response();
    }


    /**
     * 关闭字符检测
     * @param $urlId
     * @return Url
     */
    public function status_off($urlId)
    {
        if (!$item = $this->service->getUrl(['url_id' => $urlId])) {
            throw new \RuntimeException('禁止字符检测的网址不存在');
        }
        $data   = [
            'status' => SystemConst::CLOSED_ON
        ];
        $filter = [
            'url_id' => $urlId
        ];
        $this->service->setUrl($filter, $data);
        return $this->response();
    }

    /**
     * 开启字符检测
     * @param $urlId
     * @return Url
     */
    public function status_open($urlId)
    {
        if (!$item = $this->service->getUrl(['url_id' => $urlId])) {
            throw new \RuntimeException('开启字符检测的网址不存在');
        }
        if (!unserialize($item['special'])) {
            throw new \RuntimeException('您未设置特殊字符，无法开启');
        }
        $data   = [
            'status' => SystemConst::CLOSED_OFF
        ];
        $filter = [
            'url_id' => $urlId
        ];
        $this->service->setUrl($filter, $data);
        return $this->response();
    }

}
