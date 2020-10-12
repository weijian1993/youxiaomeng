<?php
/**
 * 位置服务使用的是百度的api
 * Created by PhpStorm.
 * User: 李文杰
 * Date: 2019/10/11
 * Time: 16:16
 */

namespace app\commons;


use GuzzleHttp\Client;

/**
 * 地理位置工具类
 * Class GeoHelper
 * @package app\commons
 */
class GeoHelper
{
    /**
     * 百度地图KEY
     * @var string
     */
    protected $ak = "2SMeiImLXazlpAoQc4gwfGGPnzgAz5Lx";

    /**
     * 通过IP获取位置
     * @param $ip
     * @return string
     */
    public function getLocationByIp($ip)
    {
        try {
            $url      = "http://api.map.baidu.com/location/ip?ak=" . $this->ak . '&ip=' . $ip;
            $client   = new Client();
            $result   = $client->request('GET', $url)->getBody();
            $response = json_decode($result, true);
            if ($response['status'] === 0) {
                $string = $response['content']['address'];
            } else {
                $string = '地址获取失败';
            }
            return $string;
        } catch (\Throwable  $e) {
            $string = '地址获取失败';
        }
        return $string;
    }


    /**
     * 计算某个经纬度的周围某段距离的正方形的四个点
     * @param $lng      float 目标点的经度
     * @param $lat      float 目标点的纬度
     * @param $distance float 该点所在圆的半径，该圆与此正方形内切，默认值为0.5千米
     * @return array 正方形的四个点的经纬度坐标
     */
    public function getSquarePoint($lng='', $lat='', $distance = 10)
    {
        //地球半径，平均半径为6371km
        $earthRadius = 6371;
        $dlng        = 2 * asin(sin($distance / (2 * $earthRadius)) / cos(deg2rad($lat)));
        $dlng        = rad2deg($dlng);
        $dlat        = $distance / $earthRadius;
        $dlat        = rad2deg($dlat);
        return [
            'left-top'     => ['lat' => $lat + $dlat, 'lng' => $lng - $dlng],
            'right-top'    => ['lat' => $lat + $dlat, 'lng' => $lng + $dlng],
            'left-bottom'  => ['lat' => $lat - $dlat, 'lng' => $lng - $dlng],
            'right-bottom' => ['lat' => $lat - $dlat, 'lng' => $lng + $dlng]
        ];
    }

    /**
     * 通过俩个点的经纬度,计算俩个点之间的距离,返回单位米数
     * @param $lng1 float 目标点1的经度
     * @param $lat1 float 目标点1的纬度
     * @param $lng2 float 目标点2的经度
     * @param $lat2 float 目标点2的纬度
     * @return mixed 俩点之间的直线距离
     */
    public function getDistances($lng1='', $lat1='', $lng2='', $lat2='')
    {
        //将角度转为狐度
        $radLat1 = deg2rad($lat1);//deg2rad()函数将角度转换为弧度
        $radLat2 = deg2rad($lat2);
        $radLng1 = deg2rad($lng1);
        $radLng2 = deg2rad($lng2);
        $a       = $radLat1 - $radLat2;
        $b       = $radLng1 - $radLng2;
        $s       = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6378.137 * 1000;
        return round($s, 2);
    }

    /**
     * 判断点坐标是否在一个几何多边形之内
     * @param $polyList float 该几何多边形的坐标,包含经纬度(lat,lng)的一维数组
     * @param $x        float 纬度(lat)
     * @param $y        float 经度(lng)
     * @return boolean 如果点在多边形内部，返回true， 否则返回 false;
     */
    public function isInPolygon($polyList=[], $x='', $y='')
    {
        $polyX    = $polyY = [];
        $oddNodes = false;
        foreach ($polyList as $k => $v) {
            $polyX[] = $v['lat'];
            $polyY[] = $v['lng'];
        }
        $x         = (float)$x;
        $y         = (float)$y;
        $polySides = count($polyX);
        $j         = $polySides - 1;
        for ($i = 0; $i < $polySides; $i++) {
            if (($polyY[$i] < $y && $polyY[$j] >= $y || $polyY[$j] < $y && $polyY[$i] >= $y) && ($polyX[$i] <= $x || $polyX[$j] <= $x)) {
                if ($polyX[$i] + ($y - $polyY[$i]) / ($polyY[$j] - $polyY[$i]) * ($polyX[$j] - $polyX[$i]) < $x) {
                    $oddNodes = !$oddNodes;
                }
            }
            $j = $i;
        }
        return $oddNodes;
    }

}
