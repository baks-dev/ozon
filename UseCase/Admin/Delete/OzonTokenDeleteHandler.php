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
    public function handle(
        OzonTokenDeleteDTO $command
    ): string|OzonToken {

        /** Валидация DTO  */
        $this->validatorCollection->add($command);

        $this->main = new OzonToken($command->getProfile());
        $this->event = new OzonTokenEvent();

        try
        {
            $this->preRemove($command);
        }
        catch (\DomainException $errorUniqid)
        {
            return $errorUniqid->getMessage();
        }

        /** Валидация всех объектов */
        if ($this->validatorCollection->isInvalid())
        {
            return $this->validatorCollection->getErrorUniqid();
        }

        $this->entityManager->flush();

        /* Отправляем сообщение в шину */
        $this->messageDispatch->dispatch(
            message: new OzonTokenMessage($this->main->getId(), $this->main->getEvent(), $command->getEvent()),
            transport: 'ozon'
        );

        return $this->main;
    }
}
