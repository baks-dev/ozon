<?php
/*
 *  Copyright 2023.  Baks.dev <admin@baks.dev>
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is furnished
 *  to do so, subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 */

declare(strict_types=1);

namespace BaksDev\Ozon\Api;

use BaksDev\Core\Cache\AppCacheInterface;
use BaksDev\Ozon\Repository\OzonTokenByProfile\OzonTokenByProfileInterface;
use BaksDev\Ozon\Type\Authorization\OzonAuthorizationToken;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use DomainException;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\RetryableHttpClient;
use Symfony\Contracts\Cache\CacheInterface;

abstract class Ozon
{
    protected LoggerInterface $logger;

    protected UserProfileUid|false $profile = false;

    private OzonAuthorizationToken|false $AuthorizationToken = false;

    private array $headers;

    public function __construct(
        #[Autowire(env: 'APP_ENV')] private readonly string $environment,
        private readonly OzonTokenByProfileInterface $TokenByProfile,
        private readonly AppCacheInterface $cache,
        LoggerInterface $OzonLogger,
    ) {
        $this->logger = $OzonLogger;
    }


    public function profile(UserProfileUid|string $profile): self
    {
        if(is_string($profile))
        {
            $profile = new UserProfileUid($profile);
        }

        $this->profile = $profile;

        $this->AuthorizationToken = $this->TokenByProfile->getToken($this->profile);

        return $this;
    }

    public function TokenHttpClient(OzonAuthorizationToken|false $AuthorizationToken = false): RetryableHttpClient
    {
        /**
         * @note OzonAuthorizationToken $AuthorizationToken передается в тестовом окружении
         * Если передан тестовый AuthorizationToken - присваиваем тестовый профиль
         */
        if($AuthorizationToken !== false)
        {
            $this->AuthorizationToken = $AuthorizationToken;
            $this->profile = $AuthorizationToken->getProfile();
            $this->TokenByProfile->setAuthorization($AuthorizationToken);
        }

        if($this->AuthorizationToken === false)
        {
            $this->AuthorizationToken = $this->TokenByProfile->getAuthorization();
        }

        if($this->AuthorizationToken === false)
        {
            if($this->profile === false)
            {
                $this->logger->critical('Не указан идентификатор профиля пользователя через вызов метода profile', [__FILE__.':'.__LINE__]);

                throw new InvalidArgumentException(
                    'Не указан идентификатор профиля пользователя через вызов метода profile: ->profile($UserProfileUid)'
                );
            }

            $this->AuthorizationToken = $this->TokenByProfile->getToken($this->profile);

            if($this->AuthorizationToken === false)
            {
                throw new DomainException(sprintf('Токен авторизации Ozon не найден: %s', $this->profile));
            }
        }

        $this->headers = [
            'Client-Id' => $this->getClient(),
            'Api-Key' => $this->getToken(),
        ];

        return new RetryableHttpClient(
            HttpClient::create(['headers' => $this->headers])
                ->withOptions([
                    'base_uri' => 'https://api-seller.ozon.ru',
                    'verify_host' => false
                ])
        );
    }

    /**
     * Profile
     */
    protected function getProfile(): UserProfileUid|false
    {
        return $this->profile;
    }

    protected function getToken(): string
    {
        return $this->AuthorizationToken->getToken();
    }

    protected function getClient(): string
    {
        return $this->AuthorizationToken->getClient();
    }

    protected function getWarehouse(): string
    {
        return $this->AuthorizationToken->getWarehouse();
    }

    protected function getPercent(): int
    {
        return $this->AuthorizationToken->getPercent();
    }

    public function getCacheInit(string $namespace): CacheInterface
    {
        return $this->cache->init($namespace);
    }

    /**
     * Метод проверяет что окружение является PROD,
     * тем самым позволяет выполнять операции запроса на сторонний сервис
     * ТОЛЬКО в PROD окружении
     */
    protected function isExecuteEnvironment(): bool
    {
        return $this->environment === 'prod';
    }
}
