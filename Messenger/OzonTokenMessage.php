<?php

declare(strict_types=1);

namespace BaksDev\Ozon\Messenger;

use BaksDev\Ozon\Type\Event\OzonTokenEventUid;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;

final class OzonTokenMessage
{
    /**
     * Идентификатор
     */
    private UserProfileUid $id;

    /**
     * Идентификатор события
     */
    private OzonTokenEventUid $event;

    /**
     * Идентификатор предыдущего события
     */
    private ?OzonTokenEventUid $last;


    public function __construct(UserProfileUid $id, OzonTokenEventUid $event, ?OzonTokenEventUid $last = null)
    {
        $this->id = $id;
        $this->event = $event;
        $this->last = $last;
    }


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
