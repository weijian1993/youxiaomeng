<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/5
 * Time: 16:20
 */

namespace app\models;

use think\Model;

class AdminHandleLogModel extends Model
{
    protected $pk = 'log_id';
    protected $table = 'yz_admin_handle_log';


    /**
     * 时间戳获取器
     * @param $value
     * @return false|string
     */
    public function getTimeAttr($value)
    {
        if ($value) {
            return date('Y-m-d H:i:s', $value);
        } else {
            return '空';
        }
    }


}
