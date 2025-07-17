<?php

declare(strict_types=1);

namespace BaksDev\Ozon\Repository\OzonTokensByProfile;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Ozon\Entity\Event\Active\OzonTokenActive;
use BaksDev\Ozon\Entity\Event\Card\OzonTokenCard;
use BaksDev\Ozon\Entity\Event\Client\OzonTokenClient;
use BaksDev\Ozon\Entity\Event\Percent\OzonTokenPercent;
use BaksDev\Ozon\Entity\Event\Profile\OzonTokenProfile;
use BaksDev\Ozon\Entity\Event\Stocks\OzonTokenStocks;
use BaksDev\Ozon\Entity\Event\Token\OzonTokenValue;
use BaksDev\Ozon\Entity\Event\Type\OzonTokenType;
use BaksDev\Ozon\Entity\Event\Warehouse\OzonTokenWarehouse;
use BaksDev\Ozon\Entity\OzonToken;
use BaksDev\Ozon\Type\Authorization\OzonAuthorizationToken;
use BaksDev\Ozon\Type\Id\OzonTokenUid;
use BaksDev\Users\Profile\UserProfile\Entity\UserProfile;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use Generator;
use InvalidArgumentException;


final class OzonTokensByProfileRepository implements OzonTokensByProfileInterface
{
    /** @deprecated */
    private OzonAuthorizationToken|false $authorization = false;

    /** @deprecated */
    private UserProfileUid|false $profile = false;

    public function __construct(private readonly DBALQueryBuilder $DBALQueryBuilder) {}

    /** @deprecated */
    public function forProfile(UserProfile|UserProfileUid|string $profile): self
    {
        if(empty($profile))
        {
            $this->profile = false;
            return $this;
        }

        if(is_string($profile))
        {
            $profile = new UserProfileUid($profile);
        }

        if($profile instanceof UserProfile)
        {
            $profile = $profile->getId();
        }

        $this->profile = $profile;

        return $this;
    }

    /**
     * Метод возвращает токен авторизации профиля
     *
     * @deprecated
     */
    public function getToken(): OzonAuthorizationToken|false
    {
        if(false === ($this->profile instanceof UserProfileUid))
        {
            throw new InvalidArgumentException('Invalid Argument Exception');
        }

        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);

        $dbal
            ->from(OzonToken::class, 'token');

        $dbal->join(
            'token',
            OzonTokenActive::class,
            'active',
            'active.event = token.event AND active.value IS TRUE',
        );

        $dbal
            ->addSelect('token_profile.value AS profile')
            ->join(
                'token',
                OzonTokenProfile::class,
                'token_profile',
                'token_profile.event = token.event AND token_profile.value = :profile',
            )
            ->setParameter(
                key: 'profile',
                value: $this->profile,
                type: UserProfileUid::TYPE,
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
            ->leftJoin(
                'token',
                OzonTokenType::class,
                'type',
                'type.event = token.event',
            );

        $dbal
            ->addSelect('client.value AS client')
            ->leftJoin(
                'token',
                OzonTokenClient::class,
                'client',
                'client.event = token.event',
            );

        $dbal
            ->addSelect('percent.value AS percent')
            ->leftJoin(
                'token',
                OzonTokenPercent::class,
                'percent',
                'percent.event = token.event',
            );

        $dbal
            ->addSelect('warehouse.value AS warehouse')
            ->leftJoin(
                'token',
                OzonTokenWarehouse::class,
                'warehouse',
                'warehouse.event = token.event',
            );

        $dbal
            ->addSelect('stocks.value AS stocks')
            ->leftJoin(
                'token',
                OzonTokenStocks::class,
                'stocks',
                'stocks.event = token.event',
            );

        $dbal
            ->addSelect('card.value AS card')
            ->leftJoin(
                'token',
                OzonTokenCard::class,
                'card',
                'card.event = token.event',
            );


        /* Кешируем результат ORM */
        return $this->authorization = $dbal
            ->enableCache('ozon', 86400)
            ->fetchHydrate(OzonAuthorizationToken::class);
    }


    /** @deprecated */
    public function setAuthorization(OzonAuthorizationToken $authorization): self
    {
        $this->authorization = $authorization;
        return $this;
    }

    /** @deprecated */
    public function getAuthorization(): false|OzonAuthorizationToken
    {
        return $this->authorization;
    }


    /**
     * Метод возвращает идентификаторы токенов профиля пользователя
     *
     * @return Generator<OzonTokenUid>|false
     */
    public function findAll(UserProfileUid|UserProfile $profile): Generator|false
    {
        if($profile instanceof UserProfile)
        {
            $profile = $profile->getId();
        }

        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);

        $dbal
            ->select('token.id AS value')
            ->from(OzonToken::class, 'token');

        $dbal->join(
            'token',
            OzonTokenProfile::class,
            'profile',
            'profile.event = token.event AND profile.value = :profile',
        )
            ->setParameter(
                key: 'profile',
                value: $profile,
                type: UserProfileUid::TYPE,
            );

        $dbal->join(
            'token',
            OzonTokenActive::class,
            'active',
            'active.event = token.event AND active.value IS TRUE',
        );

        return $dbal
            ->enableCache('ozon')
            ->fetchAllHydrate(OzonTokenUid::class);
    }
}
