<?php

namespace app\controller\admin\url;
use app\consts\SystemConst;
use app\controller\admin\Common;
use think\facade\View;


/**
 * Class UserController
 * @package app\controller\admin\admin
 * @property \app\service\UrlService $service
 */
class Log extends Common
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
                if (!empty($SO['url_id'])) {

                    $filter[] = [
                        'url_id', '=',$SO['url_id']
                    ];
                }
                if (!empty($SO['dateline'])) {
                    $times    = explode('-', $SO['dateline']);
                    $stime    = strtotime(str_replace('/','-',$times[0]));
                    $ltime    = strtotime(str_replace('/','-',$times[1]))+ 86399;
                    $filter[] = [
                        'dateline', 'between', [$stime, $ltime]
                    ];
                }
            }
            $orderby = [
                'dateline' => 'desc'
            ];
            $page    = $this->request->param('page', 1);
            $limit   = $this->request->param('limit', SystemConst::DEFAULT_LIMIT);
            $items   = $this->service->getLogList($filter, [], $orderby, $page, $limit);
            $count   = $this->service->getLogCount($filter);
            $filter  = [
                ['url_id', 'in', array_column($items, 'url_id')]
            ];
            $urls  = $this->service->getUrlList($filter);
            $urls   = array_column($urls, null, 'url_id');
            foreach ($items as &$item) {
                $item['title']      = $urls[$item['url_id']]['title'] ?? '网站已删除';
                $item['url']      = $urls[$item['url_id']]['url'] ?? '网站已删除';
            }
            $response = [
                'count' => $count,
                'items' => $items
            ];
            return $this->response($response);
        } else {
            $urls = $this->service->getUrlList();
            View::assign('urls', $urls);
           return  View::fetch();
        }

    }


    /**
     * 删除
     */
    public function delete()
    {
        $filter = [
            ['log_id', 'in', $this->request->post('logIds')]
        ];
        $this->service->delLoginLog($filter);
        return   $this->response();
    }


}
