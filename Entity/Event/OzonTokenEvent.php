<?php

declare(strict_types=1);

namespace BaksDev\Ozon\Entity\Event;

use BaksDev\Core\Entity\EntityEvent;
use BaksDev\Ozon\Entity\Modify\OzonTokenModify;
use BaksDev\Ozon\Entity\OzonToken;
use BaksDev\Ozon\Type\Event\OzonTokenEventUid;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;

/* OzonTokenEvent */

#[ORM\Entity]
#[ORM\Table(name: 'ozon_token_event')]
class OzonTokenEvent extends EntityEvent
{
    /**
     * Идентификатор События
     */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Id]
    #[ORM\Column(type: OzonTokenEventUid::TYPE)]
    private OzonTokenEventUid $id;

    /**
     * Профиль
     */
    #[Assert\Uuid]
    #[Assert\NotBlank]
    #[ORM\Column(type: UserProfileUid::TYPE)]
    private UserProfileUid $profile;


    /**
     * Модификатор
     */
    #[ORM\OneToOne(targetEntity: OzonTokenModify::class, mappedBy: 'event', cascade: ['all'])]
    private OzonTokenModify $modify;

    /**
     * Id Клиента
     */
    #[Assert\NotBlank]
    #[ORM\Column(type: Types::STRING)]
    private string $client;

    /**
     * Токен
     */
    #[Assert\NotBlank]
    #[ORM\Column(type: Types::TEXT)]
    private string $token;


    /**
     * Id склада
     */
    #[Assert\NotBlank]
    #[ORM\Column(type: Types::STRING, nullable: true)]
    private string $warehouse;


    /**
     * Торговая наценка
     */
    #[Assert\NotBlank]
    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $percent = null;

    /**
     * Флаг активности токена
     */
    #[Assert\Type('boolean')]
    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $active;

    /**
     * Переводы
     */
    //#[ORM\OneToMany(mappedBy: 'event', targetEntity: OzonTokenTrans::class, cascade: ['all'])]
    //private Collection $translate;


    public function __construct()
    {
        $this->id = new OzonTokenEventUid();
        $this->modify = new OzonTokenModify($this);

    }

    /**
     * Идентификатор события
     */

    public function __clone()
    {
        $this->id = clone new OzonTokenEventUid();
    }

    public function __toString(): string
    {
        return (string) $this->id;
    }

    public function getId(): OzonTokenEventUid
    {
        return $this->id;
    }

    /**
     * Идентификатор OzonToken
     */
    public function setMain(UserProfileUid|OzonToken $main): void
    {
        $this->profile = $main instanceof OzonToken ? $main->getId() : $main;
    }

    public function getMain(): ?UserProfileUid
    {
        return $this->profile;
    }

    public function getDto($dto): mixed
    {
        if($dto instanceof OzonTokenEventInterface)
        {
            return parent::getDto($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    public function setEntity($dto): mixed
    {
        if($dto instanceof OzonTokenEventInterface)
        {
            return parent::setEntity($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    public function getProfile(): UserProfileUid
    {
        return $this->profile;
    }
}
