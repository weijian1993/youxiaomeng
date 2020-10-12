<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/5
 * Time: 16:20
 */

namespace app\models;

use think\Model;

class UrlCateModel extends Model
{
    protected $pk = 'cate_id';
    protected $table = 'yz_url_cate';

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
