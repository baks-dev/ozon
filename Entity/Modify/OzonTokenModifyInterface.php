<?php

namespace BaksDev\Ozon\Entity\Modify;

use BaksDev\Core\Type\Modify\ModifyAction;

interface OzonTokenModifyInterface
{
    public function getAction(): ModifyAction;
}
