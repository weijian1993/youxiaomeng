<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/5
 * Time: 16:20
 */

namespace app\models;

use think\Model;

class AdminAuthModel extends Model
{
    protected $pk = 'auth_id';
    protected $table = 'yz_admin_auth';


    /**
     * 时间戳获取器
     * @param $value
     * @return false|string
     */
    public function getDatelineAttr($value)
    {
        if ($value) {
            return date('Y-m-d H:i:s', $value);
        } else {
            return '空';
        }
    }


}
