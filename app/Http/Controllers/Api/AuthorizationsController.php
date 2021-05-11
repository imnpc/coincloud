<?php

namespace App\Http\Controllers\Api;

use App\Traits\PassportToken;
use Illuminate\Auth\AuthenticationException;
use Psr\Http\Message\ServerRequestInterface;
use Laminas\Diactoros\Response as Psr7Response;
use League\OAuth2\Server\AuthorizationServer;
use App\Http\Requests\Api\AuthorizationRequest;
use League\OAuth2\Server\Exception\OAuthServerException;

class AuthorizationsController extends Controller
{
    use PassportToken;

    public function store(AuthorizationRequest $originRequest, AuthorizationServer $server, ServerRequestInterface $serverRequest)
    {
        try {
            return $server->respondToAccessTokenRequest($serverRequest, new Psr7Response)->withStatus(201);
        } catch (OAuthServerException $e) {
            // 拦截处理错误提示
            $type = $e->getErrorType();
            if ($type) {
                $transPayload = trans('oauth.' . $type);
                if (is_array($transPayload)) {
                    $error_msg = $transPayload['message'];
                }
            } else {
                $error_msg = $e->getMessage();
            }
            throw new AuthenticationException($error_msg);
        }
    }

    public function update(AuthorizationServer $server, ServerRequestInterface $serverRequest)
    {
        try {
            return $server->respondToAccessTokenRequest($serverRequest, new Psr7Response);
        } catch (OAuthServerException $e) {
            // 拦截处理错误提示
            $type = $e->getErrorType();
            if ($type) {
                $transPayload = trans('oauth.' . $type);
                if (is_array($transPayload)) {
                    $error_msg = $transPayload['message'];
                }
            } else {
                $error_msg = $e->getMessage();
            }
            throw new AuthenticationException($error_msg);
            //throw new AuthenticationException($e->getMessage());
        }
    }

    public function destroy()
    {
        if (auth('api')->check()) {
            auth('api')->user()->token()->revoke();
            return response(null, 204);
        } else {
            throw new AuthenticationException('The token is invalid.');
        }
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }
}
