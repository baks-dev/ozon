<?php
/*
 *  Copyright 2025.  Baks.dev <admin@baks.dev>
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is furnished
 *  to do so, subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 */

declare(strict_types=1);

namespace BaksDev\Ozon\Entity\Event\Modify\User;


use BaksDev\Core\Entity\EntityEvent;
use BaksDev\Core\Entity\EntityState;
use BaksDev\Files\Resources\Upload\UploadEntityInterface;
use BaksDev\Ozon\Entity\Event\OzonTokenEvent;
use BaksDev\Users\User\Entity\User;
use BaksDev\Users\User\Type\Id\UserUid;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * OzonTokenModifyUser
 *
 * @see OzonTokenModifyUserEvent
 */
#[ORM\Entity]
#[ORM\Table(name: 'ozon_token_modify_user')]
class OzonTokenModifyUser extends EntityEvent
{
    /** Связь на событие */
    #[Assert\NotBlank]
    #[ORM\Id]
    #[ORM\OneToOne(targetEntity: OzonTokenEvent::class, inversedBy: 'user')]
    #[ORM\JoinColumn(name: 'event', referencedColumnName: 'id')]
    private OzonTokenEvent $event;

    /** Значение свойства */
    #[ORM\Column(type: UserUid::TYPE, nullable: true)]
    private ?UserUid $value = null;

    public function __construct(OzonTokenEvent $event)
    {
        $this->event = $event;
    }

    public function __toString(): string
    {
        return (string) $this->event;
    }

    public function getValue(): UserUid
    {
        return $this->value;
    }

    public function setValue(User|UserUid $value): self
    {
        if($value instanceof User)
        {
            $value = $value->getId();
        }

        $this->value = $value;

        return $this;
    }
}