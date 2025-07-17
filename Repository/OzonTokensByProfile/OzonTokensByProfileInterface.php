<?php

namespace BaksDev\Ozon\Repository\OzonTokensByProfile;

use BaksDev\Ozon\Type\Authorization\OzonAuthorizationToken;
use BaksDev\Ozon\Type\Id\OzonTokenUid;
use BaksDev\Users\Profile\UserProfile\Entity\UserProfile;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use Generator;

interface OzonTokensByProfileInterface
{
    /** @deprecated */
    public function setAuthorization(OzonAuthorizationToken $authorization): self;

    /** @deprecated */
    public function getAuthorization(): false|OzonAuthorizationToken;


    /**
     * Метод возвращает токен профиля пользователя
     *
     * @deprecated
     */
    public function forProfile(UserProfile|UserProfileUid|string $profile): self;

    /** @deprecated */
    public function getToken(): OzonAuthorizationToken|false;


    /**
     * Метод возвращает идентификаторы токенов профиля пользователя
     *
     * @return Generator{int, OzonTokenUid}|false $var
     */
    public function findAll(UserProfile|UserProfileUid $profile): Generator|false;


}
