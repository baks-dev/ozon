<?php

namespace BaksDev\Ozon\Entity\Event;

use BaksDev\Ozon\Type\Event\OzonTokenEventUid;

interface OzonTokenEventInterface
{
    public function getEvent(): ?OzonTokenEventUid;
}
