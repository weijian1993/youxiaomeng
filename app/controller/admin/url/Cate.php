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
class Cate extends Common
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
            $filter=[];
            if ($SO = $this->request->param('SO')) {
                if (!empty($SO['title'])) {
                    $filter[] = [
                        'title', 'like', '%' . trim($SO['title']) . '%'
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
            $count   = $this->service->getCateCount($filter);
            $items   = $this->service->getCateList($filter, [], $orderby, $page, $limit);

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
     * 添加分类
     * @return mixed
     */
    public function add()
    {
        if ($this->request->isAjax()) {
            $data = [
                'title'   => $this->request->post('title', ''),//名称
                'orderby' => $this->request->post('orderby'),
            ];
            $this->service->addCate($data);
            return $this->response();
        } else {
            return View::fetch();
        }
    }


    /**
     * 修改分类
     * @param $cateId
     * @return mixed
     */
    public function edit($cateId)
    {
        if (!$detail = $this->service->getCate(['cate_id' => $cateId])) {
            throw new  \RuntimeException('修改的数据不存在');
        } else if ($this->request->isAjax()) {
            $filter = [
                'cate_id' => $cateId
            ];
            $data   = [
                'title'   => $this->request->post('title', ''),//名称
                'orderby' => $this->request->post('orderby'),

            ];
            $this->service->setCate($filter, $data);
            return $this->response();
        } else {
            View::assign('detail', $detail);
            return view::fetch();
        }
    }


    /**
     * 删除分类
     */
    public function delete()
    {
        $cateIds = $this->request->post('cateIds');
        foreach ($cateIds as $cateId) {
            $this->service->delCate(['cate_id' => $cateId]);
        }
        return $this->response();
    }

}
