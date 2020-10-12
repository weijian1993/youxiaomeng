<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/5
 * Time: 16:20
 */

namespace app\models;

use think\Model;

class AdminRoleModel extends Model
{
    protected $pk = 'role_id';
    protected $table = 'yz_admin_role';


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

    /**
     * 权限获取器
     * @param $value
     * @return array
     */
    public function getPrivAttr($value)
    {
        if ($value){
            return explode(',',$value);
        }else{
            return [];
        }
    }


}
