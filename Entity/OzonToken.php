<?php

declare(strict_types=1);

namespace BaksDev\Ozon\Entity;

use BaksDev\Ozon\Entity\Event\OzonTokenEvent;
use BaksDev\Ozon\Type\Event\OzonTokenEventUid;
use BaksDev\Ozon\Type\Id\OzonTokenUid;
use BaksDev\Users\Profile\UserProfile\Entity\UserProfile;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
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
    #[ORM\Column(type: UserProfileUid::TYPE)]
    private UserProfileUid $id;

    /**
     * Идентификатор события
     */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Column(type: OzonTokenEventUid::TYPE, unique: true)]
    private OzonTokenEventUid $event;

    public function __construct(UserProfile|UserProfileUid $profile)
    {
        $this->id = $profile instanceof UserProfile ? $profile->getId() : $profile;
    }

    public function __toString(): string
    {
        return (string) $this->id;
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

    public function setEvent(OzonTokenEventUid|OzonTokenEvent $event): void
    {
        $this->event = $event instanceof OzonTokenEvent ? $event->getId() : $event;
    }
}
