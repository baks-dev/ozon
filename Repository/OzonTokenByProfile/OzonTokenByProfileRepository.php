<?php

declare(strict_types=1);

namespace BaksDev\Ozon\Repository\OzonTokenByProfile;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Ozon\Entity\Event\OzonTokenEvent;
use BaksDev\Ozon\Entity\OzonToken;
use BaksDev\Ozon\Type\Authorization\OzonAuthorizationToken;
use BaksDev\Users\Profile\UserProfile\Entity\Info\UserProfileInfo;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use BaksDev\Users\Profile\UserProfile\Type\UserProfileStatus\Status\UserProfileStatusActive;
use BaksDev\Users\Profile\UserProfile\Type\UserProfileStatus\UserProfileStatus;

final class OzonTokenByProfileRepository implements OzonTokenByProfileInterface
{
    public function __construct(private DBALQueryBuilder $DBALQueryBuilder)
    {
    }

    /**
     * Метод возвращает токен авторизации профиля
     */
    public function getToken(UserProfileUid|string $profile): ?OzonAuthorizationToken
    {
        if(is_string($profile))
        {
            $profile = new UserProfileUid($profile);
        }

        $qb = $this->DBALQueryBuilder->createQueryBuilder(self::class);


        $qb
            ->from(OzonToken::class, 'token')
            ->where('token.id = :profile')
            ->setParameter('profile', $profile, UserProfileUid::TYPE);

        $qb->join(
            'token',
            OzonTokenEvent::class,
            'event',
            'event.id = token.event AND event.active = true',
        );

        $qb
            ->join(
                'token',
                UserProfileInfo::class,
                'info',
                'info.profile = token.id AND info.status = :status',
            )
            ->setParameter(
                'status',
                UserProfileStatusActive::class,
                UserProfileStatus::TYPE
            );

        $qb->select('token.id AS profile');
        $qb->addSelect('event.token AS token');
        $qb->addSelect('event.client AS client');

        /* Кешируем результат ORM */
        return $qb
            ->enableCache('ozon', 86400)
            ->fetchHydrate(OzonAuthorizationToken::class);

    }
}
