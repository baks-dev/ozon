<?php

declare(strict_types=1);

namespace BaksDev\Ozon\UseCase\Admin\NewEdit;

use BaksDev\Ozon\Entity\Event\OzonTokenEventInterface;
use BaksDev\Ozon\Type\Event\OzonTokenEventUid;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use Symfony\Component\Validator\Constraints as Assert;

/** @see OzonTokenEvent */
final class OzonTokenDTO implements OzonTokenEventInterface
{
    /**
     * Идентификатор события
     */
    #[Assert\Uuid]
    private ?OzonTokenEventUid $id = null;

    /**
     * Токен
     */
    #[Assert\NotBlank]
    private ?string $token;

    /**
     * Id Клиента
     */
    #[Assert\NotBlank]
    private string $client;

    /**
     * Id склада
     */
    #[Assert\NotBlank]
    private string $warehouse;

    /**
     * Торговая наценка
     */
    #[Assert\NotBlank]
    #[Assert\Range(min: 0, max: 100)]
    private int $percent = 0;

    /**
     * Профиль
     */
    #[Assert\NotBlank]
    private ?UserProfileUid $profile = null;

    /**
     * Флаг активности токена
     */
    private bool $active;


    /**
     * Идентификатор события
     */
    public function getEvent(): ?OzonTokenEventUid
    {
        return $this->id;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }


    public function setToken(?string $token): void
    {
        if(!empty($token))
        {
            $this->token = $token;
        }
    }

    public function getProfile(): ?UserProfileUid
    {
        return $this->profile;
    }

    public function setProfile(UserProfileUid $profile): void
    {
        $this->profile = $profile;
    }

    public function getClient(): string
    {
        return $this->client;
    }

    public function setClient(string $client): void
    {
        $this->client = $client;
    }

    /**
     * Warehouse
     */
    public function getWarehouse(): string
    {
        return $this->warehouse;
    }

    public function setWarehouse(string $warehouse): self
    {
        $this->warehouse = $warehouse;
        return $this;
    }

    public function getActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function hiddenToken(): void
    {
        $this->token = null;
    }

    public function getPercent(): int
    {
        return $this->percent;
    }

    public function setPercent(int $percent): self
    {
        $this->percent = $percent;
        return $this;
    }

}
