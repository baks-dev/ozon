<?php

declare(strict_types=1);

namespace BaksDev\Ozon\Messenger;

use BaksDev\Ozon\Type\Event\OzonTokenEventUid;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;

final class OzonTokenMessage
{
    public function __construct(
        private readonly UserProfileUid $id,
        private readonly OzonTokenEventUid $event,
        private readonly ?OzonTokenEventUid $last = null
    ) {}

    /**
     * Идентификатор
     */
    public function getId(): UserProfileUid
    {
        return $this->id;
    }


    /**
     * Идентификатор события
     */
    public function getEvent(): OzonTokenEventUid
    {
        return $this->event;
    }

    /**
     * Идентификатор предыдущего события
     */
    public function getLast(): ?OzonTokenEventUid
    {
        return $this->last;
    }

}
