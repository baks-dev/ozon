<?php

namespace BaksDev\Ozon\Repository\OzonTokensByProfile;

use BaksDev\Ozon\Type\Id\OzonTokenUid;
use BaksDev\Users\Profile\UserProfile\Entity\UserProfile;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use Generator;

interface OzonTokensByProfileInterface
{
    public function andNotActive(): self;

    public function onlyCardUpdate(): self;

    /**
     * Метод возвращает идентификаторы токенов профиля пользователя
     *
     * @return Generator<int, OzonTokenUid>|false $var
     */
    public function findAll(UserProfile|UserProfileUid $profile): Generator|false;
}
