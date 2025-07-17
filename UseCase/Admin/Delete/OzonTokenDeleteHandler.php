<?php

declare(strict_types=1);

namespace BaksDev\Ozon\UseCase\Admin\Delete;

use BaksDev\Core\Entity\AbstractHandler;
use BaksDev\Ozon\Entity\Event\OzonTokenEvent;
use BaksDev\Ozon\Entity\OzonToken;
use BaksDev\Ozon\Messenger\OzonTokenMessage;

final class OzonTokenDeleteHandler extends AbstractHandler
{
    /** @see Ozon */
    public function handle(OzonTokenDeleteDTO $command): string|OzonToken
    {
        $this->setCommand($command);

        $this->preEventRemove(OzonToken::class, OzonTokenEvent::class);

        /** Валидация всех объектов */
        if($this->validatorCollection->isInvalid())
        {
            return $this->validatorCollection->getErrorUniqid();
        }

        $this->flush();

        /* Отправляем сообщение в шину */
        $this->messageDispatch->dispatch(
            message: new OzonTokenMessage($this->main->getId(), $this->main->getEvent(), $command->getEvent()),
            transport: 'ozon'
        );

        return $this->main;
    }
}
