<?php

declare(strict_types=1);

namespace BaksDev\Ozon\Messenger;

use BaksDev\Ozon\Repository\OzonTokenCurrentEvent\OzonTokenCurrentEventInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(priority: 0)]
final class OzonTokenHandler
{
    public function __construct(
        private readonly OzonTokenCurrentEventInterface $currentEvent,
    ) {
    }

    public function __invoke(OzonTokenMessage $message): void
    {
    }
}
