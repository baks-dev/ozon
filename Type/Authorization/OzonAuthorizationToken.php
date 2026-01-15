<?php
/*
 *  Copyright 2023.  Baks.dev <admin@baks.dev>
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

namespace BaksDev\Ozon\Type\Authorization;

use BaksDev\Users\Profile\TypeProfile\Type\Id\TypeProfileUid;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;

final class OzonAuthorizationToken
{
    /**
     * ID настройки (профиль пользователя)
     */
    private readonly string $profile;

    public function __construct(
        UserProfileUid|string $profile,
        private readonly string $token,
        private readonly string $type,
        private readonly string $client,
        private readonly string $warehouse,
        private readonly string $percent,

        private int $vat,
        private ?bool $card = false, // карточки
        private ?bool $stocks = false, // остатки
        private ?bool $orders = false, // заказы
        private ?bool $sales = false, // продажи
    )
    {

        $this->profile = (string) $profile;

    }

    public function getProfile(): UserProfileUid
    {
        return new UserProfileUid($this->profile);
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getClient(): string
    {
        return $this->client;
    }

    public function getPercent(): string
    {
        return (string) ($this->percent ?: 0);
    }

    public function getWarehouse(): string
    {
        return $this->warehouse;
    }

    public function isCard(): bool
    {
        return $this->card === true;
    }

    public function isStocks(): bool
    {
        return $this->stocks === true;
    }

    public function isOrders(): ?bool
    {
        return $this->orders === true;
    }

    public function isSales(): ?bool
    {
        return $this->sales === true;
    }

    public function getVat(): string|false
    {
        return match (true)
        {
            empty($this->vat) => '0',
            $this->vat === 5 => '0.05',
            $this->vat === 7 => '0.07',
            $this->vat === 10 => '0.1',
            $this->vat === 20 => '0.2',
            default => false,
        };
    }

    public function getType(): TypeProfileUid
    {
        return new TypeProfileUid($this->type);
    }


}
