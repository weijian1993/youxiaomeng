<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/5
 * Time: 16:20
 */

namespace app\models;

use think\Model;

class UrlModel extends Model
{
    protected $pk = 'url_id';
    protected $table = 'yz_url';

    /**
     * 时间戳获取器
     * @param $value
     * @return array|mixed
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