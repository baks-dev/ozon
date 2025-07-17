<?php

declare(strict_types=1);

namespace BaksDev\Ozon\Entity;

use BaksDev\Ozon\Entity\Event\OzonTokenEvent;
use BaksDev\Ozon\Type\Event\OzonTokenEventUid;
use BaksDev\Ozon\Type\Id\OzonTokenUid;
use BaksDev\Users\Profile\UserProfile\Entity\UserProfile;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/* OzonToken */

#[ORM\Entity]
#[ORM\Table(name: 'ozon_token')]
class OzonToken
{
    /**
     * Идентификатор сущности
     */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Id]
    #[ORM\Column(type: OzonTokenUid::TYPE)]
    private OzonTokenUid $id;

    /**
     * Идентификатор события
     */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Id]
    #[ORM\Column(type: OzonTokenEventUid::TYPE)]
    private OzonTokenEventUid $event;

    public function __construct()
    {
        $this->id = new OzonTokenUid();
    }

    public function __toString(): string
    {
        return (string) $this->id;
    }

    /**
     * Идентификатор
     */
    public function getId(): OzonTokenUid
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

    public function setEvent(OzonTokenEventUid|OzonTokenEvent $event): void
    {
        $this->event = $event instanceof OzonTokenEvent ? $event->getId() : $event;
    }
}
