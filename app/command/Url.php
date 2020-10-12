<?php
declare (strict_types = 1);

namespace app\command;

use app\service\UrlService;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;

class Url extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('url')
            ->setDescription('检测url异常');
    }

    protected function execute(Input $input, Output $output)
    {
        //只需要开启检测网站
        $filter = [
            'closed' => 0,
        ];
        /** @var UrlService $urlService */
        $urlService = app('urlService');
        $items      = $urlService->getUrlList($filter, []);
        $curl       = curl_init();//开启url
        foreach ($items as $v) {
            $url = $v['url'];
            $t1  = microtime(true);//计时
            curl_setopt($curl, CURLOPT_URL, $url); //设置URL
            curl_setopt($curl, CURLOPT_HEADER, 1); //获取Header
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); //数据存到成字符串吧
            $fh        = curl_exec($curl); //获取到页面
            $t2        = microtime(true);//关闭计时
            $code      = curl_getinfo($curl, CURLINFO_HTTP_CODE); //状态码
            $time_get  = curl_getinfo($curl, CURLINFO_PRETRANSFER_TIME); //响应时间

            $time_load = $t2 - $t1;//加载时间
            $info[]    = [];
            if ($code == 200) {//不等于200统统报错
                if ($time_load > 3) {
                    $info[] = [
                        'info'     => '网站访问速度过慢',
                        'url_id'   => $v['url_id'],
                        'dateline' => time(),
                        'code'     => $code,
                        'speed'    => $time_load,//加载速度
                        'get_time' => $time_get,//响应速度
                        'title'    => $v['title'],
                        'url'      => $v['url'],
                    ];
                }
                if ($v['status'] == 0) {//判断是否是检测字符
                    $special = unserialize($v['special']);
                    if ($special) {
                        $infoData = [];
                        foreach ($special as $vv) {
                            if (substr_count($fh, $vv) <= 0) {
                                $infoData = [
                                    'info'     => '未匹配到特殊字符',
                                    'url_id'   => $v['url_id'],
                                    'dateline' => time(),
                                    'code'     => $code,
                                    'speed'    => $time_load,//加载速度
                                    'get_time' => $time_get,//响应速度
                                    'title'    => $v['title'],
                                    'url'      => $v['url'],
                                ];
                            }
                        }
                        if ($infoData) {
                            $info[] = $infoData;//只通知一次
                        }
                    }

                }
            } else {
                $info[] = [
                    'info'     => '状态码错误',
                    'url_id'   => $v['url_id'],
                    'dateline' => time(),
                    'code'     => $code,
                    'title'    => $v['title'],
                    'url'      => $v['url'],
                ];
            }
        }
        curl_close($curl); //关闭url
        if ($info) {
            try {
                $urlService->batchUrlLog($info);
                foreach ($info as $v) {
                    if ($v) {
                        $textString = json_encode([
                            'msgtype' => 'text',
                            'text'    => [
                                "content" => "URL" . "网站-" . $v['title'] . ',' . '网址-' . $v['url'] . "，出现异常，请及时处理。"
                            ],
                        ]);
                        $urlService->request_by_curl($textString);
                    }
                }
                return true;
            } catch (\Throwable $e) {
                throw new \RuntimeException('计划任务失败');
            }
        }
    }
}
