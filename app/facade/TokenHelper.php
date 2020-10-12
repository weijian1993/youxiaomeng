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
 * 用户token生成类
 * Class TokenHelper
 * @package app\facade
 */
class TokenHelper extends Facade
{

    protected static function getFacadeClass()
    {
        return 'app\commons\TokenHelper';
    }

}
