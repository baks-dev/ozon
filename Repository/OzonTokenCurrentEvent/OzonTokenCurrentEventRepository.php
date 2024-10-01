<?php

declare(strict_types=1);

namespace BaksDev\Ozon\Repository\OzonTokenCurrentEvent;

use BaksDev\Core\Doctrine\ORMQueryBuilder;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use BaksDev\Ozon\Entity\Event\OzonTokenEvent;
use BaksDev\Ozon\Entity\OzonToken;

final readonly class OzonTokenCurrentEventRepository implements OzonTokenCurrentEventInterface
{
    public function __construct(private ORMQueryBuilder $ORMQueryBuilder)
    {
    }

    /** Метод возвращает активное событие токена профиля */
    public function findByProfile(UserProfileUid|string $profile): OzonTokenEvent|false
    {
        if(is_string($profile))
        {
            $profile = new UserProfileUid($profile);
        }

        $orm = $this->ORMQueryBuilder->createQueryBuilder(self::class);

        $orm
            ->from(OzonToken::class, 'main')
            ->where('main.id = :profile')
            ->setParameter('profile', $profile, UserProfileUid::TYPE);

        $orm
            ->select('event')
            ->join(
                OzonTokenEvent::class,
                'event',
                'WITH',
                'event.id = main.event'
            );

        return $orm->getQuery()->getOneOrNullResult() ?: false;
    }
}
