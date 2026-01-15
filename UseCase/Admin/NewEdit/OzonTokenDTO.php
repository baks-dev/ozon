<?php

declare(strict_types=1);

namespace BaksDev\Ozon\UseCase\Admin\NewEdit;

use BaksDev\Ozon\Entity\Event\OzonTokenEventInterface;
use BaksDev\Ozon\Type\Event\OzonTokenEventUid;
use BaksDev\Ozon\UseCase\Admin\NewEdit\Active\OzonTokenActiveDTO;
use BaksDev\Ozon\UseCase\Admin\NewEdit\Card\OzonTokenCardDTO;
use BaksDev\Ozon\UseCase\Admin\NewEdit\Client\OzonTokenClientDTO;
use BaksDev\Ozon\UseCase\Admin\NewEdit\Name\OzonTokenNameDTO;
use BaksDev\Ozon\UseCase\Admin\NewEdit\Orders\OzonTokenOrdersDTO;
use BaksDev\Ozon\UseCase\Admin\NewEdit\Percent\OzonTokenPercentDTO;
use BaksDev\Ozon\UseCase\Admin\NewEdit\Profile\OzonTokenProfileDTO;
use BaksDev\Ozon\UseCase\Admin\NewEdit\Sales\OzonTokenSalesDTO;
use BaksDev\Ozon\UseCase\Admin\NewEdit\Stocks\OzonTokenStocksDTO;
use BaksDev\Ozon\UseCase\Admin\NewEdit\Type\OzonTokenTypeDTO;
use BaksDev\Ozon\UseCase\Admin\NewEdit\Value\OzonTokenValueDTO;
use BaksDev\Ozon\UseCase\Admin\NewEdit\Vat\OzonTokenVatDTO;
use BaksDev\Ozon\UseCase\Admin\NewEdit\Warehouse\OzonTokenWarehouseDTO;
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
     * Название
     */
    #[Assert\Valid]
    private OzonTokenNameDTO $name;

    /**
     * Id Клиента
     */
    #[Assert\Valid]
    private OzonTokenClientDTO $client;

    /**
     * Профиль
     */
    #[Assert\Valid]
    private OzonTokenProfileDTO $profile;

    /**
     * Флаг активности токена
     */
    #[Assert\Valid]
    private OzonTokenActiveDTO $active;


    /**
     * Торговая наценка
     */
    #[Assert\Valid]
    private OzonTokenPercentDTO $percent;

    /**
     * Токен
     */
    #[Assert\Valid]
    private OzonTokenValueDTO $token;

    /**
     * Тип доставки
     */
    #[Assert\Valid]
    private OzonTokenTypeDTO $type;

    /**
     * Идентификатор склада
     */
    #[Assert\Valid]
    private OzonTokenWarehouseDTO $warehouse;

    /**
     * Обновлять карточки продукта
     */
    #[Assert\Valid]
    private OzonTokenCardDTO $card;

    /**
     * Обновлять остатки
     */
    #[Assert\Valid]
    private OzonTokenStocksDTO $stocks;

    /**
     * Остановить продажи
     */
    #[Assert\Valid]
    private OzonTokenSalesDTO $sales;

    /**
     * Обновлять заказы
     */
    #[Assert\Valid]
    private OzonTokenOrdersDTO $orders;


    /**
     * НДС, применяемый для товара
     */
    #[Assert\Valid]
    private OzonTokenVatDTO $vat;


    public function __construct()
    {
        $this->type = new OzonTokenTypeDTO();
        $this->profile = new OzonTokenProfileDTO();
        $this->active = new OzonTokenActiveDTO();
        $this->card = new OzonTokenCardDTO();
        $this->stocks = new OzonTokenStocksDTO();
        $this->sales = new OzonTokenSalesDTO();
        $this->orders = new OzonTokenOrdersDTO();
        $this->percent = new OzonTokenPercentDTO();
        $this->token = new OzonTokenValueDTO();
        $this->client = new OzonTokenClientDTO();
        $this->name = new OzonTokenNameDTO();
        $this->warehouse = new OzonTokenWarehouseDTO();
        $this->vat = new OzonTokenVatDTO();
    }

    public function getEvent(): ?OzonTokenEventUid
    {
        return $this->id;
    }

    public function getProfile(): OzonTokenProfileDTO
    {
        return $this->profile;
    }

    public function getName(): OzonTokenNameDTO
    {
        return $this->name;
    }

    public function getClient(): OzonTokenClientDTO
    {
        return $this->client;
    }

    public function getToken(): OzonTokenValueDTO
    {
        return $this->token;
    }

    public function getActive(): OzonTokenActiveDTO
    {
        return $this->active;
    }

    public function getPercent(): OzonTokenPercentDTO
    {
        return $this->percent;
    }


    public function getCard(): OzonTokenCardDTO
    {
        return $this->card;
    }

    public function getStocks(): OzonTokenStocksDTO
    {
        return $this->stocks;
    }

    public function getWarehouse(): OzonTokenWarehouseDTO
    {
        return $this->warehouse;
    }

    public function getVat(): OzonTokenVatDTO
    {
        return $this->vat;
    }

    public function getType(): OzonTokenTypeDTO
    {
        return $this->type;
    }

    public function getOrders(): OzonTokenOrdersDTO
    {
        return $this->orders;
    }

    public function getSales(): OzonTokenSalesDTO
    {
        return $this->sales;
    }
}