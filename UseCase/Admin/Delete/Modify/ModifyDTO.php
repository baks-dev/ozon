<?php

declare(strict_types=1);

namespace BaksDev\Ozon\UseCase\Admin\Delete\Modify;

use BaksDev\Core\Type\Modify\Modify\ModifyActionDelete;
use BaksDev\Core\Type\Modify\ModifyAction;
use BaksDev\Ozon\Entity\Modify\OzonTokenModifyInterface;
use Symfony\Component\Validator\Constraints as Assert;

/** @see ModifyEvent */
final class ModifyDTO implements OzonTokenModifyInterface
{
    /**
     * Модификатор
     */
    private readonly ModifyAction $action;


    public function __construct()
    {
        $this->action = new ModifyAction(ModifyActionDelete::class);
    }

    public function getAction(): ModifyAction
    {
        return $this->action;
    }

}
