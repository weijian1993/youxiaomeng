<?php
/**
 * Created by PhpStorm.
 * User: 李文杰
 * Date: 2019/4/23
 * Time: 16:39
 */

namespace app\facade;
use think\Facade;

/**
 * Class RegexVerify
 * @package app\facade
 * @see     \app\commons\GeoHelper
 * @mixin \app\commons\GeoHelper
 * @method mixed getLocationByIp(string $ip) static 通过IP获取地址
 */
class GeoHelper extends Facade
{

    protected static function getFacadeClass()
    {
        return 'app\commons\GeoHelper';
    }

}
