<?php

declare(strict_types=1);

namespace BaksDev\Ozon\Messenger;

use BaksDev\Ozon\Type\Event\OzonTokenEventUid;
use BaksDev\Ozon\Type\Id\OzonTokenUid;

final class OzonTokenMessage
{
    private string $id;
    private string $event;
    private string|false $last;

    public function __construct(
        OzonTokenUid $id,
        OzonTokenEventUid $event,
        ?OzonTokenEventUid $last = null
    )
    {

        $this->id = (string) $id;
        $this->event = (string) $event;
        $this->last = ($last instanceof OzonTokenEventUid) ? (string) $last : false;
    }

    /**
     * Id
     */
    public function getId(): OzonTokenUid
    {
        return new OzonTokenUid($this->id);
    }

    /**
     * Event
     */
    public function getEvent(): OzonTokenEventUid
    {
        return new OzonTokenEventUid($this->event);
    }

    /**
     * Last
     */
    public function getLast(): OzonTokenEventUid|false
    {
        return $this->last ? new OzonTokenEventUid($this->last) : false;
    }


}
