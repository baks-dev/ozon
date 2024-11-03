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

namespace BaksDev\Ozon\Repository\AllProfileToken;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Ozon\Entity\Event\OzonTokenEvent;
use BaksDev\Ozon\Entity\OzonToken;
use BaksDev\Users\Profile\UserProfile\Entity\Info\UserProfileInfo;
use BaksDev\Users\Profile\UserProfile\Entity\Personal\UserProfilePersonal;
use BaksDev\Users\Profile\UserProfile\Entity\UserProfile;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use BaksDev\Users\Profile\UserProfile\Type\UserProfileStatus\Status\UserProfileStatusActive;
use BaksDev\Users\Profile\UserProfile\Type\UserProfileStatus\UserProfileStatus;
use Generator;

final class AllProfileOzonTokenRepository implements AllProfileOzonTokenInterface
{
    private bool $active = false;

    public function __construct(private readonly DBALQueryBuilder $DBALQueryBuilder) {}

    public function onlyActiveToken(): self
    {
        $this->active = true;
        return $this;
    }

    /**
     * Метод возвращает идентификаторы профилей всех добавленных токенов
     */
    public function findAll(): Generator
    {
        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);

        $dbal->from(OzonToken::class, 'token');

        if($this->active)
        {
            $dbal->join(
                'token',
                OzonTokenEvent::class,
                'event',
                'event.id = token.event AND event.active = true',
            );
        }

        $dbal
            ->join(
                'token',
                UserProfileInfo::class,
                'users_profile_info',
                'users_profile_info.profile = token.id AND users_profile_info.status = :status',
            )
            ->setParameter(
                'status',
                UserProfileStatusActive::class,
                UserProfileStatus::TYPE
            );

        $dbal->join(
            'users_profile_info',
            UserProfile::class,
            'users_profile',
            'users_profile.id = users_profile_info.profile',
        );


        $dbal->leftJoin(
            'users_profile',
            UserProfilePersonal::class,
            'personal',
            'personal.event = users_profile.event',
        );


        /** Параметры конструктора объекта гидрации */
        $dbal->addSelect('token.id AS value');
        $dbal->addSelect('personal.username AS attr');

        return $dbal
            ->enableCache('ozon', '1 minutes')
            ->fetchAllHydrate(UserProfileUid::class);

    }
}
