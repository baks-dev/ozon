<?php
/*
 *  Copyright 2025.  Baks.dev <admin@baks.dev>
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

namespace BaksDev\Ozon\Repository\OzonToken;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Ozon\Entity\Event\Active\OzonTokenActive;
use BaksDev\Ozon\Entity\Event\Card\OzonTokenCard;
use BaksDev\Ozon\Entity\Event\Client\OzonTokenClient;
use BaksDev\Ozon\Entity\Event\Percent\OzonTokenPercent;
use BaksDev\Ozon\Entity\Event\Profile\OzonTokenProfile;
use BaksDev\Ozon\Entity\Event\Stocks\OzonTokenStocks;
use BaksDev\Ozon\Entity\Event\Token\OzonTokenValue;
use BaksDev\Ozon\Entity\Event\Type\OzonTokenType;
use BaksDev\Ozon\Entity\Event\Vat\OzonTokenVat;
use BaksDev\Ozon\Entity\Event\Warehouse\OzonTokenWarehouse;
use BaksDev\Ozon\Entity\OzonToken;
use BaksDev\Ozon\Type\Authorization\OzonAuthorizationToken;
use BaksDev\Ozon\Type\Id\OzonTokenUid;
use BaksDev\Users\Profile\UserProfile\Entity\Event\Info\UserProfileInfo;
use BaksDev\Users\Profile\UserProfile\Type\UserProfileStatus\Status\UserProfileStatusActive;
use BaksDev\Users\Profile\UserProfile\Type\UserProfileStatus\UserProfileStatus;
use InvalidArgumentException;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(public: true)]
final class OzonTokenRepository implements OzonTokenInterface
{
    private OzonTokenUid|false $identifier = false;

    private OzonAuthorizationToken|false $authorization = false;

    private bool $active = true;

    public function __construct(private readonly DBALQueryBuilder $DBALQueryBuilder) {}

    public function andNotActive(): self
    {
        $this->active = false;
        return $this;
    }

    public function forTokenIdentifier(OzonToken|OzonTokenUid|string $identifier): self
    {
        if(empty($identifier))
        {
            $this->identifier = false;
            return $this;
        }

        if(is_string($identifier))
        {
            $identifier = new OzonTokenUid($identifier);
        }

        if($identifier instanceof OzonToken)
        {
            $identifier = $identifier->getId();
        }

        $this->identifier = $identifier;

        return $this;
    }

    /**
     * Метод возвращает токен авторизации профиля
     */
    public function find(): OzonAuthorizationToken|false
    {
        if($this->authorization instanceof OzonAuthorizationToken)
        {
            return $this->authorization;
        }

        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);

        if(false === ($this->identifier instanceof OzonTokenUid))
        {
            throw new InvalidArgumentException('Invalid Argument OzonToken');
        }

        $dbal
            ->from(OzonToken::class, 'token')
            ->where('token.id = :token')
            ->setParameter(
                key: 'token',
                value: $this->identifier,
                type: OzonTokenUid::TYPE,
            );

        if($this->active === true)
        {
            $dbal
                ->join(
                    'token',
                    OzonTokenActive::class,
                    'active',
                    'active.event = token.event AND active.value IS TRUE',
                );
        }

        $dbal
            ->addSelect('profile.value AS profile')
            ->join(
                'token',
                OzonTokenProfile::class,
                'profile',
                'profile.event = token.event',
            );

        $dbal
            ->addSelect('token_value.value AS token')
            ->join(
                'token',
                OzonTokenValue::class,
                'token_value',
                'token_value.event = token.event',
            );

        $dbal
            ->addSelect('type.value AS type')
            ->join(
                'token',
                OzonTokenType::class,
                'type',
                'type.event = token.event',
            );


        $dbal
            ->addSelect('client.value AS client')
            ->join(
                'token',
                OzonTokenClient::class,
                'client',
                'client.event = token.event',
            );


        $dbal
            ->addSelect('warehouse.value AS warehouse')
            ->join(
                'token',
                OzonTokenWarehouse::class,
                'warehouse',
                'warehouse.event = token.event',
            );


        $dbal
            ->addSelect('percent.value AS percent')
            ->join(
                'token',
                OzonTokenPercent::class,
                'percent',
                'percent.event = token.event',
            );

        $dbal
            ->addSelect('vat.value AS vat')
            ->leftJoin(
                'token',
                OzonTokenVat::class,
                'vat',
                'vat.event = token.event',
            );

        $dbal
            ->addSelect('card.value AS card')
            ->leftJoin(
                'token',
                OzonTokenCard::class,
                'card',
                'card.event = token.event',
            );

        $dbal
            ->addSelect('stocks.value AS stocks')
            ->leftJoin(
                'token',
                OzonTokenStocks::class,
                'stocks',
                'stocks.event = token.event',
            );


        /* Кешируем результат ORM */
        return $dbal
            ->enableCache('ozon')
            ->fetchHydrate(OzonAuthorizationToken::class);
    }

    public function setAuthorization(OzonAuthorizationToken $authorization): self
    {
        $this->authorization = $authorization;
        return $this;
    }

    public function getAuthorization(): false|OzonAuthorizationToken
    {
        return $this->authorization;
    }
}