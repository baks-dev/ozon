<?php

declare(strict_types=1);

namespace BaksDev\Ozon\Type\Event;

use BaksDev\Core\Type\UidType\UidType;

final class OzonTokenEventType extends UidType
{
    public function getClassType(): string
    {
        return OzonTokenEventUid::class;
    }

    public function getName(): string
    {
        return OzonTokenEventUid::TYPE;
    }
}
