<?php

namespace BaksDev\Ozon\Repository\OzonTokenCurrentEvent;

use BaksDev\Ozon\Entity\Event\OzonTokenEvent;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;

interface OzonTokenCurrentEventInterface
{
    public function findByProfile(UserProfileUid|string $profile): OzonTokenEvent|false;
}
