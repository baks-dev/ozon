<?php

declare(strict_types=1);

namespace BaksDev\Ozon\Entity\Event;

use BaksDev\Core\Entity\EntityEvent;
use BaksDev\Ozon\Entity\Event\Active\OzonTokenActive;
use BaksDev\Ozon\Entity\Event\Card\OzonTokenCard;
use BaksDev\Ozon\Entity\Event\Client\OzonTokenClient;
use BaksDev\Ozon\Entity\Event\Modify\Action\OzonTokenModifyAction;
use BaksDev\Ozon\Entity\Event\Modify\DateTime\OzonTokenModifyDateTime;
use BaksDev\Ozon\Entity\Event\Modify\IpAddress\OzonTokenModifyIpAddress;
use BaksDev\Ozon\Entity\Event\Modify\User\OzonTokenModifyUser;
use BaksDev\Ozon\Entity\Event\Modify\UserAgent\OzonTokenModifyUserAgent;
use BaksDev\Ozon\Entity\Event\Name\OzonTokenName;
use BaksDev\Ozon\Entity\Event\Percent\OzonTokenPercent;
use BaksDev\Ozon\Entity\Event\Profile\OzonTokenProfile;
use BaksDev\Ozon\Entity\Event\Stocks\OzonTokenStocks;
use BaksDev\Ozon\Entity\Event\Token\OzonTokenValue;
use BaksDev\Ozon\Entity\Event\Type\OzonTokenType;
use BaksDev\Ozon\Entity\Event\Vat\OzonTokenVat;
use BaksDev\Ozon\Entity\Event\Warehouse\OzonTokenWarehouse;
use BaksDev\Ozon\Entity\OzonToken;
use BaksDev\Ozon\Type\Event\OzonTokenEventUid;
use BaksDev\Ozon\Type\Id\OzonTokenUid;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
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
    #[ORM\Column(type: OzonTokenUid::TYPE)]
    private OzonTokenUid $main;


    /**
     * Название
     */
    #[ORM\OneToOne(targetEntity: OzonTokenName::class, mappedBy: 'event', cascade: ['all'])]
    private OzonTokenName $name;


    /**
     * Флаг активности токена
     */
    #[ORM\OneToOne(targetEntity: OzonTokenActive::class, mappedBy: 'event', cascade: ['all'])]
    private OzonTokenActive $active;

    /**
     * Торговая наценка
     */
    #[ORM\OneToOne(targetEntity: OzonTokenPercent::class, mappedBy: 'event', cascade: ['all'])]
    private OzonTokenPercent $percent;


    /**
     * Профиль пользователя
     */
    #[ORM\OneToOne(targetEntity: OzonTokenProfile::class, mappedBy: 'event', cascade: ['all'])]
    private OzonTokenProfile $profile;

    /**
     * Токен
     */
    #[ORM\OneToOne(targetEntity: OzonTokenValue::class, mappedBy: 'event', cascade: ['all'])]
    private OzonTokenValue $token;

    /**
     * Тип расчета стоимости услуг
     */
    #[ORM\OneToOne(targetEntity: OzonTokenType::class, mappedBy: 'event', cascade: ['all'])]
    private OzonTokenType $type;

    /**
     * Id Клиента
     */
    #[ORM\OneToOne(targetEntity: OzonTokenClient::class, mappedBy: 'event', cascade: ['all'])]
    private OzonTokenClient $client;

    /**
     * Идентификатор склада
     */
    #[ORM\OneToOne(targetEntity: OzonTokenWarehouse::class, mappedBy: 'event', cascade: ['all'])]
    private OzonTokenWarehouse $warehouse;

    /**
     * Обновлять карточки продукта
     */
    #[ORM\OneToOne(targetEntity: OzonTokenCard::class, mappedBy: 'event', cascade: ['all'])]
    private ?OzonTokenCard $card = null;

    /**
     * Запустить продажи
     */
    #[ORM\OneToOne(targetEntity: OzonTokenStocks::class, mappedBy: 'event', cascade: ['all'])]
    private ?OzonTokenStocks $stocks = null;

    /**
     * НДС, применяемый для товара
     */
    #[ORM\OneToOne(targetEntity: OzonTokenVat::class, mappedBy: 'event', cascade: ['all'])]
    private ?OzonTokenVat $vat = null;

    /**
     * Модификатор
     */

    /** OzonTokenModifyAction */
    #[ORM\OneToOne(targetEntity: OzonTokenModifyAction::class, mappedBy: 'event', cascade: ['all'])]
    private ?OzonTokenModifyAction $action = null;

    /** OzonTokenModifyDateTime */
    #[ORM\OneToOne(targetEntity: OzonTokenModifyDateTime::class, mappedBy: 'event', cascade: ['all'])]
    private OzonTokenModifyDateTime $datetime;

    /** OzonTokenModifyUserAgent */
    #[ORM\OneToOne(targetEntity: OzonTokenModifyUserAgent::class, mappedBy: 'event', cascade: ['all'])]
    private ?OzonTokenModifyUserAgent $agent = null;

    /** OzonTokenModifyIpAddress */
    #[ORM\OneToOne(targetEntity: OzonTokenModifyIpAddress::class, mappedBy: 'event', cascade: ['all'])]
    private ?OzonTokenModifyIpAddress $ipv = null;

    /** OzonTokenModifyUser */
    #[ORM\OneToOne(targetEntity: OzonTokenModifyUser::class, mappedBy: 'event', cascade: ['all'])]
    private ?OzonTokenModifyUser $user = null;


    public function __construct()
    {
        $this->id = new OzonTokenEventUid();

        $this->action = new OzonTokenModifyAction($this);
        $this->datetime = new OzonTokenModifyDateTime($this);
        $this->agent = new OzonTokenModifyUserAgent($this);
        $this->user = new OzonTokenModifyUser($this);
        $this->ipv = new OzonTokenModifyIpAddress($this);
    }

    /**
     * Идентификатор события
     */

    public function __clone()
    {
        $this->id = clone new OzonTokenEventUid();
        $this->datetime = clone $this->datetime;
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
    public function setMain(OzonTokenUid|OzonToken $main): void
    {
        $this->main = $main instanceof OzonToken ? $main->getId() : $main;
    }

    public function getMain(): ?OzonTokenUid
    {
        return $this->main;
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
        return $this->profile->getValue();
    }

    public function equalsTokenProfile(UserProfileUid $profile): bool
    {
        return $this->profile->getValue()->equals($profile);
    }

    public function getAction(): OzonTokenModifyAction
    {
        return $this->action ?: $this->action = new OzonTokenModifyAction($this);
    }

    public function getAgent(): OzonTokenModifyUserAgent
    {
        return $this->agent ?: $this->agent = new OzonTokenModifyUserAgent($this);
    }

    public function getIpAddress(): OzonTokenModifyIpAddress
    {
        return $this->ipv ?: $this->ipv = new OzonTokenModifyIpAddress($this);
    }

    public function getUser(): OzonTokenModifyUser
    {
        return $this->user ?: $this->user = new OzonTokenModifyUser($this);
    }
}
