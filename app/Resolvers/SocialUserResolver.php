<?php

namespace App\Resolvers;

use Exception;
use Coderello\SocialGrant\Resolvers\SocialUserResolverInterface;
use Illuminate\Contracts\Auth\Authenticatable;
use Laravel\Socialite\Facades\Socialite;
use App\Services\SocialAccountsService;

class SocialUserResolver implements SocialUserResolverInterface
{
    /**
     * Resolve user by provider credentials.
     * 通过凭据解析获得用户数据
     * Oauth1 协议的额外参数使用 request('对应key') 获取
     *
     * @param string $provider
     * @param string $accessToken
     *
     * @return Authenticatable|null
     */
    public function resolveUserByProviderCredentials(string $provider, string $accessToken): ?Authenticatable
    {
        $providerUser = null;
        try {
            if ($provider == 'twitter') {
                $accessTokenSecret = request('access_token_secret');
                $providerUser = Socialite::driver($provider)->userFromTokenAndSecret($accessToken, $accessTokenSecret);
            } else {
                $providerUser = Socialite::driver($provider)->userFromToken($accessToken);
            }
        } catch (Exception $exception) {
        }

        if ($providerUser) {
            if ($provider == 'twitter') {
                return (new SocialAccountsService())->one($providerUser, $provider);
            } else {
                return (new SocialAccountsService())->two($providerUser, $provider);
            }
        }
        return null;
    }
}
