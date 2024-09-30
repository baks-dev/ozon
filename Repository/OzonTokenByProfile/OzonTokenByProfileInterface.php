<?php

namespace BaksDev\Ozon\Repository\OzonTokenByProfile;

use BaksDev\Ozon\Type\Authorization\OzonAuthorizationToken;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;

interface OzonTokenByProfileInterface
{
    public function setAuthorization(OzonAuthorizationToken $authorization): self;

    public function getAuthorization(): false|OzonAuthorizationToken;

    public function getToken(UserProfileUid|string $profile): OzonAuthorizationToken|false;
}
