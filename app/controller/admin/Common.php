<?php

namespace app\controller\admin;


use app\BaseController;
use app\consts\ProviderConst;
use app\consts\SystemConst;
use app\middleware\CheckAdmin;
use app\middleware\UrlLog;
use think\facade\Request;
use think\facade\Validate;

class Common extends BaseController
{
    /**
     * 中间件
     * @var array
     */
    protected $middleware = [CheckAdmin::class];


    /**
     * 坐标选择器
     * @param float $lng
     * @param float $lat
     * @return mixed
     */
    public function chooseMap($lng = 117.227308, $lat = 31.82057)
    {
        $jsKey  = config('map.map_js_key');
        $mapKey = config('map.map_web_key');
        $this->assign('lng', $lng);
        $this->assign('lat', $lat);
        $this->assign('jsKey', $jsKey);
        $this->assign('mapKey', $mapKey);
        return $this->fetch();
    }

    /**
     * 图片上传
     * @throws \RuntimeException
     */
    public function uploadImg()
    {
        if (!$photos = Request::file('photo')) {
            throw new \RuntimeException('请选择要上传的图片');
        }
        /** @var SystemService $systemService */
        $systemService = $this->getServices(ProviderConst::SYSTEM_SERVICE);
        $config        = $systemService->getKey(SystemConst::CONFIG_FILESITE);
        $data          = [];
        foreach ($photos as $photo) {
            $path   = $systemService->uploadImg($photo, $config['v']);
            $path   = str_replace('\\', '/', $path);
            $data[] = $path;
        }

        $result = [
            'error' => 0,
            'msg'   => 'success',
            'data'  => [
                'photo' => $data
            ]
        ];
        return json($result);
    }

    /**
     * 上传水印图片
     */
    public function uploadWater()
    {
        if (!$water = Request::file('water')) {
            throw new \RuntimeException('请选择要上传的图片');
        } else if (Image::open($water)->type() != 'png') {
            throw new \RuntimeException('图片水印只能用png格式');
        }
        Image::open($water)->save(storage_path('water/water.png'));;
        $this->response();
    }


    /**
     * 短信发送
     * @param $mobile
     * @throws \RuntimeException
     */
    public function sendSms($mobile)
    {
        if (!(Validate::is($mobile, 'mobile'))) {
            throw new \RuntimeException('手机号格式不正确');
        }
        /** @var SystemService $systemService */
        $systemService = $this->getServices(ProviderConst::SYSTEM_SERVICE);
        $systemService->sendSms($mobile, false);
        $this->response();
    }


}
