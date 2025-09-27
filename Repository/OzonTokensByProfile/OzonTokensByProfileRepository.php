<?php

declare(strict_types=1);

namespace BaksDev\Ozon\Repository\OzonTokensByProfile;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Ozon\Entity\Event\Active\OzonTokenActive;
use BaksDev\Ozon\Entity\Event\Card\OzonTokenCard;
use BaksDev\Ozon\Entity\Event\Profile\OzonTokenProfile;
use BaksDev\Ozon\Entity\OzonToken;
use BaksDev\Ozon\Type\Id\OzonTokenUid;
use BaksDev\Users\Profile\UserProfile\Entity\UserProfile;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use Generator;
use InvalidArgumentException;


final class OzonTokensByProfileRepository implements OzonTokensByProfileInterface
{
    private bool $active;

    /** TRUE - только токены с флагом обновления карточки товаров */
    private bool $card;

    private UserProfileUid|false $profile = false;

    public function __construct(private readonly DBALQueryBuilder $DBALQueryBuilder) {}

    public function forProfile(UserProfileUid|UserProfile $profile): self
    {
        /**  По умолчанию */
        $this->active = true;
        $this->card = false;

        if($profile instanceof UserProfile)
        {
            $profile = $profile->getId();
        }

        $this->profile = $profile;

        return $this;
    }

    public function onlyCardUpdate(): self
    {
        $this->card = true;
        return $this;
    }

    public function andNotActive(): self
    {
        $this->active = false;
        return $this;
    }

    /**
     * Метод возвращает идентификаторы токенов профиля пользователя
     *
     * @return Generator<OzonTokenUid>|false
     */
    public function findAll(): Generator|false
    {
        if(false === ($this->profile instanceof UserProfileUid))
        {
            throw new InvalidArgumentException('Не указан профиль пользователя');
        }

        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);

        $dbal
            ->select('token.id AS value')
            ->from(OzonToken::class, 'token');

        $dbal
            ->join(
                'token',
                OzonTokenProfile::class,
                'profile',
                'profile.event = token.event AND profile.value = :profile',
            )
            ->setParameter(
                key: 'profile',
                value: $this->profile,
                type: UserProfileUid::TYPE,
            );


        if(true === $this->active)
        {
            $dbal->join(
                'token',
                OzonTokenActive::class,
                'active',
                'active.event = token.event AND active.value IS TRUE',
            );
        }

        if(true === $this->card)
        {
            $dbal->join(
                'token',
                OzonTokenCard::class,
                'card',
                'card.event = token.event AND card.value IS TRUE',
            );
        }

        return $dbal
            ->enableCache('ozon')
            ->fetchAllHydrate(OzonTokenUid::class);
    }


}
