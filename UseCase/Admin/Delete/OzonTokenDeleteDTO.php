<?php

declare(strict_types=1);

namespace BaksDev\Ozon\UseCase\Admin\Delete;

use BaksDev\Ozon\Entity\Event\OzonTokenEventInterface;
use BaksDev\Ozon\Type\Event\OzonTokenEventUid;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use Symfony\Component\Validator\Constraints as Assert;

/** @see OzonTokenEvent */
final class OzonTokenDeleteDTO implements OzonTokenEventInterface
{
    /**
     * Идентификатор события
     */
    #[Assert\Uuid]
    private ?OzonTokenEventUid $id = null;

    #[Assert\NotBlank]
    private readonly UserProfileUid $profile;

    /**
     * Идентификатор события
     */
    public function getEvent(): ?OzonTokenEventUid
    {
        return $this->id;
    }

    public function getProfile(): UserProfileUid
    {
        return $this->profile;
    }

    public function setProfile(UserProfileUid $profile): void
    {
        $this->profile = $profile;
    }

}
