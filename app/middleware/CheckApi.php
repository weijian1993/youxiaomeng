<?php

namespace app\middleware;


use app\consts\ProviderConst;
use think\Container;
use think\facade\Request;

class CheckApi
{
    /**
     * 权限验证
     * @param          $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {


        return $next($request);
    }
}
