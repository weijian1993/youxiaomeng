<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
/**
 * 打印参数
 */
if (!function_exists('dd')) {
    function dd(...$args)
    {
        echo '<pre>';
        foreach ($args as $arg) {
            print_r($arg);
            echo '<br>';
        }
        exit;
    }

}

/**
 * 获取仓库路径
 */
if (!function_exists('storage_path')) {
    function storage_path($path)
    {
        return '.' . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . $path;
    }
}

/**
 * 递归树形处理
 */
if (!function_exists('formatTree')) {
    function formatTree($items, $pid = 0, $level = 1, $field = ['pid' => 'pid', 'id' => 'id', 'child' => 'child'])
    {
        $result = [];
        foreach ($items as &$item) {
            if ($item[$field['pid']] == $pid) {
                $item['level']         = $level;
                $item[$field['child']] = formatTree($items, $item[$field['id']], $item['level'] + 1, $field) ?? [];
                $result[]              = $item;
            }
        }
        return $result;
    }
}

/**
 * 递归排序处理
 */
if (!function_exists('formatSort')) {
    function formatSort($items, $pid = 0, $level = 1, $field = ['pid' => 'pid', 'id' => 'id'], &$result = [])
    {
        foreach ($items as $item) {
            if ($item[$field['pid']] == $pid) {
                $item['level'] = $level;
                $result[]      = $item;
                formatSort($items, $item[$field['id']], $item['level'] + 1, $field, $result);
            }
        }
        return $result;
    }
}

/**
 * 数组过滤
 */
if (!function_exists('arrayFilter')) {
    function arrayFilter($items, $fields)
    {
        foreach ($items as $k => $item) {
            if (!in_array($k, $fields)) {
                unset($items[$k]);
            }
        }
        return $items;
    }
}

/**
 * 计算2个时间之间的月份数（不论前后顺序）
 * @param   $startTime   开始时间（亦可截止时间）
 * @param   $endTime     截止时间（亦可开始时间）
 * @return int
 */
if (!function_exists('getMonthCount')) {
    function getMonthCount($startTime = 0, $endTime = 0)
    {
        $part     = 0;
        $max_time = 2145888000; // 时间戳即将溢出值,防止后面溢出死循环 2038-01-01 00:00:00 (UTC)
        if (($startTime = (int)$startTime) && ($endTime = (int)$endTime) && $startTime < $max_time && $endTime < $max_time) {
            if ($startTime > $endTime) {
                $tempTime  = $startTime;
                $startTime = $endTime;
                $endTime   = $tempTime;
            }
            $startTime = date('Y-m', $startTime);// 不加 d 从 1号计算， 此处要与 max_time 计算一致
            $dateUnit  = 'month';
            while (true) {
                if ($nextDate = date('Y-m-d', strtotime("$startTime + $part $dateUnit"))) {//得到下一个具体的日期
                    $next_time = strtotime("$nextDate");
                    if ($next_time > $endTime) {// 前面保证了 next_time 值不会溢出
                        break;
                    }
                    $part++;
                }
            }
        }
        return $part;
    }
}

/**
 * 获取2个时间之间的月份数组（不论前后顺序） {int}
 * @param   $startTime   开始时间（亦可截止时间）
 * @param   $endTime     截止时间（亦可开始时间）
 * @return  mixed      array value is timestamp
 */
if (!function_exists('getDateList')) {
    function getDateList($startTime = 0, $endTime = 0)
    {
        $dateList = [];
        if ($month_count = getMonthCount($startTime, $endTime)) {
            $firstday = date("Y-m-01");
            $dateList = [0 => $firstday];
            for ($i = 1; $i < $month_count; $i++) {
                $firstday     = date("Y-m-d", strtotime("$firstday -1 month"));
                $dateList[$i] = $firstday;
            }
            foreach ($dateList as $k => $v) {
                $dateList[$k] = strtotime($v);
            }
        }
        return $dateList;
    }
}

/**
 * 获取一个月的每一天的时间
 */
if (!function_exists('getDaylist')) {
    function getDaylist($mouth = '')
    {
        $days  = [];
        $mouth = empty($mouth) ? date('Ymd') : $mouth;
        if ($mouth) {
            $startTime = date('Y-m-01', strtotime($mouth));  //获取本月第一天时间戳
            $endTime   = date('Y-m-d', strtotime("$startTime +1 month"));
            $date      = range(0, 31);
            foreach ($date as $v) {
                if ((strtotime($startTime) + ($v * 86400)) < (strtotime($endTime) - 1)) {
                    $days[] = date('Ymd', (strtotime($startTime) + ($v * 86400)));
                }

            }
        }
        return $days;
    }
}

/*
 * 获取当前月天数 或 指定1 ~ 31天 日期数组 (图表X轴)
 * @param date $unix
 * return Array
 */
if (!function_exists('getDays')) {
    function getDays($unix)
    {
        $dayList = [];
        if ($unix = (int)$unix) {
            if ($unix <= 31) {
                if ($beginTime = strtotime(date('Y-m-d') . "-{$unix} day")) {// 起始日
                    for ($i = 0; $i < $unix; $i++) {
                        $dayList[] = date("m-d", $i * 86400 + $beginTime);
                    }
                }
            } else {
                $month     = date('m', $unix);
                $year      = date('Y', $unix);
                $nextMonth = (($month + 1) > 12) ? 1 : ($month + 1);
                $year      = ($nextMonth > 12) ? ($year + 1) : $year;
                $days      = date('d', mktime(0, 0, 0, $nextMonth, 0, $year));
                for ($i = 1; $i <= $days; $i++) {
                    $dayList[] = date("{$month}-{$i}");
                }
            }
        }
        return $dayList;
    }
}

/**
 * 获取俩个时间戳间的日期
 * @param int    $starTime 开始时间
 * @param int    $endTime  结束时间
 * @param string $type     类型 d=>天 h=>小时
 * @return array
 */
if (!function_exists('getMouthArray')) {
    function getMouthArray($starTime = 0, $endTime = 0, $type = 'd')
    {
        $arr = array();
        switch ($type) {
            case 'd':
                $days  = ceil(($endTime - $starTime) / 86400) + 1;
                $begin = date('Ymd', $endTime - ($days - 1) * 86400);
                for ($i = 0; $i < $days; $i++) {
                    $t       = strtotime("{$begin} +{$i} day");
                    $v       = date('m/d', $t);
                    $k       = (int)date('Ymd', $t);
                    $arr[$k] = $v;
                }
                break;
            case "h";
                for ($i = 57600; $i <= 144000; $i = $i + 3600) {
                    $arr[(int)date("H", $i)] = date("H:i", $i);
                }
                break;

        }
        return $arr;

    }


    /**
     * 获取当前请求路径，不带后缀
     */
    if (!function_exists('path')) {
        function path(\app\Request $request)
        {
            $path = trim($request->pathinfo() . '.html');
            return $path;
        }
    }
    if (!function_exists('CheckUrl')) {
        //检测域名格式
        function CheckUrl($C_url)
        {
            $str = "/^http(s?):\/\/(?:[A-za-z0-9-]+\.)+[A-za-z]{2,4}(?:[\/\?#][\/=\?%\-&~`@[\]\':+!\.#\w]*)?$/";
            if (!preg_match($str, $C_url)) {
                return false;
            } else {
                return true;
            }
        }
    }
}

