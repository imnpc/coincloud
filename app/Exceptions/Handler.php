<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Database\QueryException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        InvalidRequestException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
    /**
     * Report or log an exception.
     *
     * @param \Exception $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $e)
    {
        parent::report($e);
    }

    public function render($request, Throwable $e)
    {
        if ($request->is('api/*') || $request->is('oauth/*') || $request->is('sendcode')) {
            $response = [];
            $error = $this->convertExceptionToResponse($e);
            $error_msg = empty($e->getMessage()) ? 'something error' : $e->getMessage();
            $error_code = $error->getStatusCode();
            $error_msg_404 = "404 Not Found";
            $error_route_msg_404 = "Route 404 Not Found";
            $error_model_msg_404 = "Data 404 Not Found";
            if ($e instanceof AuthenticationException) { // 未授权
                $error_code = Response::HTTP_UNAUTHORIZED;
                if ($request->is('*/authorization*')) { // 登录授权验证控制器
                    $response['message'] = $error_msg;
                } elseif ($error_msg = "Unauthenticated") {
                    $response['code'] = Response::HTTP_UNAUTHORIZED;
                    $response['data']['message'] = $error_msg;
                } else {
                    $response['message'] = $error_msg;
                }
            } elseif ($e instanceof ValidationException) { // 验证规则错误
                $error_code = Response::HTTP_UNPROCESSABLE_ENTITY;
                $response['message'] = $e->validator->errors()->first(); // 获取第一条的错误
            } elseif ($e instanceof NotFoundHttpException) {
                //路由未找到
                $error_code = Response::HTTP_NOT_FOUND;
                $response['code'] = Response::HTTP_NOT_FOUND;
                $response['data']['message'] = $error_route_msg_404;
            } elseif ($e instanceof ModelNotFoundException) {
                // 模型未找到
                $error_code = Response::HTTP_NOT_FOUND;
                $response['message'] = $error_model_msg_404;
            } elseif ($e instanceof MethodNotAllowedHttpException) { // 请求方法错误
                $error_code = Response::HTTP_METHOD_NOT_ALLOWED;
                $response['code'] = Response::HTTP_METHOD_NOT_ALLOWED;
                $response['data']['message'] = $error_msg;
            } elseif ($e instanceof QueryException) { // 查询或者更新数据错误
                $error_code = Response::HTTP_FORBIDDEN;
                if (env('APP_DEBUG')) {
                    $response['message'] = $e->getPrevious()->getMessage();
                } else {
                    //$response['message'] = "Please check if something is empty";
                    $response['message'] = $e->getPrevious()->getMessage();
                }
            } elseif ($e instanceof ThrottleRequestsException) { // 请求次数频繁 超过限制
                $error_code = Response::HTTP_TOO_MANY_REQUESTS;
                $response['code'] = Response::HTTP_TOO_MANY_REQUESTS;
                $response['data']['message'] = $error_msg;
            } else { // 其他错误
                $class = get_class($e);
                if ($class == 'Laravel\Passport\Exceptions\OAuthServerException') { // Passport 错误
                    $error_code = Response::HTTP_UNAUTHORIZED;
                    $type = $e->getPrevious()->getErrorType();
                    if ($type) {
                        $transPayload = trans('oauth.' . $type);
                        if (is_array($transPayload)) {
                            $error_msg = $transPayload['message'];
                        }
                    }
                }
                $response['message'] = $error_msg;
            }
            return response()->json($response, $error_code);
        } else {
            return parent::render($request, $e);
        }
    }
}
