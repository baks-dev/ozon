<?php

namespace BaksDev\Ozon\Repository\OzonTokenCurrentEvent;

use BaksDev\Ozon\Entity\Event\OzonTokenEvent;
use BaksDev\Ozon\Entity\OzonToken;
use BaksDev\Ozon\Type\Id\OzonTokenUid;
use BaksDev\Users\Profile\UserProfile\Entity\UserProfile;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(public: true)]
interface OzonTokenCurrentEventInterface
{
    /** Метод возвращает активное событие токена профиля */
    public function find(OzonToken|OzonTokenUid $token): OzonTokenEvent|false;
}
