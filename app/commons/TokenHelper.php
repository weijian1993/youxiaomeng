<?php
/**
 * Created by PhpStorm.
 * User: 李文杰
 * Date: 2019/4/23
 * Time: 16:41
 */

namespace app\commons;


use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;

class TokenHelper
{
    protected $secret = "blues@)!*";

    /**
     * 生成token
     * @param $id
     * @return string
     */
    public function createToken($id = 0, $day = 7)
    {
        try {
            $time    = $day * 86400;
            $builder = new Builder();
            $signer  = new Sha256();
            // 发布者
            $builder->setIssuer("blues.com")
                //接收者
                ->setAudience("blues.com")
                //对当前token设置的标识
                ->setId("abc", true)
                //token创建时间
                ->setIssuedAt(time())
                //token过期时间
                ->setExpiration(time() + $time)
                //当前时间在这个时间前，token不能使用
                ->setNotBefore(time())
                //自定义数据
                ->set('id', $id);
            //设置签名
            $builder->sign($signer, $this->secret);
            //获取加密后的token，转为字符串
            $token = (string)$builder->getToken();
            return $token;
        } catch (\Throwable $e) {
            throw new \RuntimeException('登陆失败');
        }
    }

    /**
     * 解密token
     * @param $token
     * @return array|bool
     */
    public function parseToken($token)
    {
        try {
            $signer = new Sha256();
            //解析token
            $parse = (new Parser())->parse($token);
            //验证token合法性
            if (!$parse->verify($signer, $this->secret)) {
                throw new \RuntimeException('登陆状态不合法');
            }
            //验证是否已经过期
            if ($parse->isExpired()) {
                throw new \RuntimeException('登陆状态已过期');
            }
            //获取数据
            $id = $parse->getClaim('id');
            return $id;
        } catch (\RuntimeException $e) {
            throw new \RuntimeException('error');
        }
    }

}
