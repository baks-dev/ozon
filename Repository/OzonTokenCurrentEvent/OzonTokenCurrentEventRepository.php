<?php

declare(strict_types=1);

namespace BaksDev\Ozon\Repository\OzonTokenCurrentEvent;

use BaksDev\Core\Doctrine\ORMQueryBuilder;
use BaksDev\Ozon\Entity\Event\OzonTokenEvent;
use BaksDev\Ozon\Entity\OzonToken;
use BaksDev\Ozon\Type\Id\OzonTokenUid;

final class OzonTokenCurrentEventRepository implements OzonTokenCurrentEventInterface
{
    private OzonTokenUid|false $token = false;

    public function __construct(private readonly ORMQueryBuilder $ORMQueryBuilder) {}

    /** Метод возвращает активное событие токена профиля */
    public function find(OzonToken|OzonTokenUid $token): OzonTokenEvent|false
    {
        $orm = $this->ORMQueryBuilder->createQueryBuilder(self::class);

        $orm
            ->from(OzonToken::class, 'main')
            ->where('main.id = :token')
            ->setParameter(
                key: 'token',
                value: $token instanceof OzonToken ? $token->getId() : $token,
                type: OzonTokenUid::TYPE,
            );

        $orm
            ->select('event')
            ->join(
                OzonTokenEvent::class,
                'event',
                'WITH',
                'event.id = main.event',
            );

        return $orm->getOneOrNullResult() ?: false;
    }
}
