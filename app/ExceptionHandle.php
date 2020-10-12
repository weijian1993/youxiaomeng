<?php

namespace app;

use app\consts\ErrorCodeConst;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\Handle;
use think\exception\HttpException;
use think\exception\HttpResponseException;
use think\exception\ValidateException;
use think\Response;
use Throwable;

/**
 * 应用异常处理类
 */
class ExceptionHandle extends Handle
{
    /**
     * 不需要记录信息（日志）的异常类列表
     * @var array
     */
    protected $ignoreReport = [
        HttpException::class,
        HttpResponseException::class,
        ModelNotFoundException::class,
        DataNotFoundException::class,
        ValidateException::class,
    ];

    /**
     * 记录异常信息（包括日志或者其它方式记录）
     *
     * @access public
     * @param  Throwable $exception
     * @return void
     */
    public function report(Throwable $exception): void
    {
        // 使用内置的方式记录异常日志
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @access public
     * @param \think\Request $request
     * @param Throwable      $e
     * @return Response
     */
    public function render($request, Throwable $e): Response
    {
        // 添加自定义异常处理机制
        if ($e instanceof ValidateException) {
            return json($e->getError(), 422);
        } else if ($e instanceof HttpException) {
            return parent::render($e);
        } else if ($e instanceof \RuntimeException) {
            $result = [
                'msg'   => $e->getMessage(),
                'error' => $e->getCode() > 0 ? $e->getCode() : ErrorCodeConst::ERROR
            ];

            if ($request->isAjax() || substr($request->pathinfo(), 0, 3) == 'api') {
                return json($result, 200);
            } else {
                return json($result, 200);
            }
        } else {
            $msg    = [
                'msg'  => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ];
            $result = [
                "msg"   => $msg,
                "error" => 999
            ];
            return json($result, 500);
        }
        // 其他错误交给系统处理
        return parent::render($request, $e);
    }
}
