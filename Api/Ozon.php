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
use BaksDev\Ozon\Entity\OzonToken;
use BaksDev\Ozon\Orders\Type\ProfileType\TypeProfileFbsOzon;
use BaksDev\Ozon\Repository\OzonToken\OzonTokenInterface;
use BaksDev\Ozon\Repository\OzonTokensByProfile\OzonTokensByProfileInterface;
use BaksDev\Ozon\Type\Authorization\OzonAuthorizationToken;
use BaksDev\Ozon\Type\Id\OzonTokenUid;
use BaksDev\Users\Profile\TypeProfile\Type\Id\TypeProfileUid;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use DomainException;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\RetryableHttpClient;
use Symfony\Contracts\Cache\CacheInterface;

abstract class Ozon
{
    protected UserProfileUid|false $profile = false;

    protected OzonTokenUid|false $identifier = false;

    private OzonAuthorizationToken|false $AuthorizationToken = false;

    private array $headers;

    public function __construct(
        #[Autowire(env: 'APP_ENV')] private readonly string $environment,
        #[Target('ozonLogger')] protected readonly LoggerInterface $logger,
        private readonly OzonTokenInterface $OzonToken,
        private readonly OzonTokensByProfileInterface $OzonTokensByProfile,
        private readonly AppCacheInterface $cache,
    ) {}


    public function forTokenIdentifier(OzonToken|OzonTokenUid|UserProfileUid $identifier): self
    {
        /**
         * Если передан идентификатор профиля пользователя UserProfileUid -
         * получаем один идентификатор профиля с флагом Card = true
         */
        if($identifier instanceof UserProfileUid)
        {
            $tokensByProfile = $this->OzonTokensByProfile
                ->forProfile($identifier)
                ->onlyCardUpdate()
                ->findAll();

            if(false !== $tokensByProfile && false !== $tokensByProfile->valid())
            {
                /** @var OzonTokenUid $identifier */
                $identifier = $tokensByProfile->current();
            }
        }

        if($identifier instanceof OzonToken)
        {
            $identifier = $identifier->getId();
        }

        if(false === ($identifier instanceof OzonTokenUid))
        {
            $this->profile = false;
            $this->identifier = false;
            return $this;
        }

        $this->AuthorizationToken = $this->OzonToken
            ->forTokenIdentifier($identifier)
            ->find();

        $this->identifier = $identifier;

        return $this;
    }


    public function TokenHttpClient(OzonAuthorizationToken|false $AuthorizationToken = false): RetryableHttpClient
    {
        /**
         * @note OzonAuthorizationToken $AuthorizationToken передается в тестовом окружении
         * Если передан тестовый AuthorizationToken - присваиваем тестовый профиль
         */
        if($AuthorizationToken instanceof OzonAuthorizationToken)
        {
            $this->AuthorizationToken = $AuthorizationToken;
            $this->profile = $AuthorizationToken->getProfile();
            $this->OzonToken->setAuthorization($AuthorizationToken);
        }

        if(false === ($this->AuthorizationToken instanceof OzonAuthorizationToken))
        {
            $this->AuthorizationToken = $this->OzonToken->getAuthorization();
        }

        if(false === ($this->AuthorizationToken instanceof OzonAuthorizationToken))
        {
            if(false === ($this->identifier instanceof OzonTokenUid))
            {
                throw new InvalidArgumentException(
                    'Не указан идентификатор токена профиля пользователя через вызов метода forIdentifier: ->forIdentifier($OzonTokenUid)',
                );
            }

            $this->AuthorizationToken = $this->OzonToken
                ->forTokenIdentifier($this->identifier)
                ->find();

            if(false === ($this->AuthorizationToken instanceof OzonAuthorizationToken))
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
                    'verify_host' => false,
                ]),
        );
    }


    protected function getProfile(): UserProfileUid|false
    {
        if(false === ($this->AuthorizationToken instanceof OzonAuthorizationToken))
        {
            return false;
        }

        return $this->AuthorizationToken->getProfile();
    }

    public function getIdentifier(): false|OzonTokenUid
    {
        return $this->identifier;
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

    protected function getPercent(): string
    {
        return $this->AuthorizationToken->getPercent();
    }

    protected function isCard(): bool
    {
        return
            ($this->AuthorizationToken instanceof OzonAuthorizationToken)
            && $this->AuthorizationToken->isCard() === true;
    }

    protected function isStocks(): bool
    {
        return
            ($this->AuthorizationToken instanceof OzonAuthorizationToken)
            && $this->AuthorizationToken->isStocks() === true;
    }

    protected function getType(): TypeProfileUid
    {
        return $this->AuthorizationToken->getType();
    }

    protected function getVat(): string|false
    {
        return $this->AuthorizationToken->getVat();
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
