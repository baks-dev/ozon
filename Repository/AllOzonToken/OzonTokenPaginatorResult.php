<?php
/*
 *  Copyright 2026.  Baks.dev <admin@baks.dev>
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

namespace BaksDev\Ozon\Repository\AllOzonToken;

use BaksDev\Auth\Email\Type\Email\AccountEmail;
use BaksDev\Auth\Email\Type\EmailStatus\EmailStatus;
use BaksDev\Ozon\Type\Event\OzonTokenEventUid;
use BaksDev\Ozon\Type\Id\OzonTokenUid;
use BaksDev\Users\Profile\TypeProfile\Type\Id\TypeProfileUid;
use BaksDev\Users\Profile\UserProfile\Type\Event\UserProfileEventUid;
use BaksDev\Users\Profile\UserProfile\Type\UserProfileStatus\UserProfileStatus;
use DateTimeImmutable;

final readonly class OzonTokenPaginatorResult
{
    public function __construct(
        private string $id, // "01978694-98ff-7e22-ae35-8b621ffbe981"
        private string $event, // "019b97da-7f52-7b48-9309-1d89cf524b3b"
        private string $date, // "2026-01-07 12:47:17"
        private string $name, // "Митино DBS"
        private ?bool $active, // true
        private ?bool $card, // false
        private ?bool $orders, // null
        private ?bool $sales, // null
        private ?bool $stocks, // false
        private string $type, // "c024b3d2-1866-72c3-83e9-922f8678bf23"
        private string $users_profile_event, // "019a7449-16d9-74c4-b24c-f22366a0780b"
        private string $users_profile_status, // "act"
        private string $users_profile_username, // "White Sign"
        private ?string $users_profile_avatar, // null
        private ?string $users_profile_avatar_ext, // null
        private ?bool $users_profile_avatar_cdn, // null
        private ?string $account_email, // "baf.green@yandex.ru"
        private ?string $account_status, // "act"
    ) {}

    public function getId(): OzonTokenUid
    {
        return new OzonTokenUid($this->id);
    }

    public function getEvent(): OzonTokenEventUid
    {
        return new OzonTokenEventUid($this->event);
    }

    public function getDate(): DateTimeImmutable
    {
        return new DateTimeImmutable($this->date);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getActive(): bool
    {
        return $this->active === true;
    }

    public function getCard(): bool
    {
        return $this->card === true;
    }

    public function getOrders(): bool
    {
        return $this->orders === true;
    }

    public function getSales(): bool
    {
        return $this->sales === true;
    }

    public function getStocks(): bool
    {
        return $this->stocks === true;
    }

    public function getType(): TypeProfileUid
    {
        return new TypeProfileUid($this->type);
    }

    public function getUsersProfileEvent(): UserProfileEventUid
    {
        return new UserProfileEventUid($this->users_profile_event);
    }

    public function getUsersProfileStatus(): UserProfileStatus
    {
        return new UserProfileStatus($this->users_profile_status);
    }

    public function getUsersProfileUsername(): string
    {
        return $this->users_profile_username;
    }

    public function getUsersProfileAvatar(): ?string
    {
        return $this->users_profile_avatar;
    }

    public function getUsersProfileAvatarExt(): ?string
    {
        return $this->users_profile_avatar_ext;
    }

    public function getUsersProfileAvatarCdn(): bool
    {
        return $this->users_profile_avatar_cdn === true;
    }

    public function getAccountEmail(): AccountEmail
    {
        return new AccountEmail($this->account_email);
    }

    public function getAccountStatus(): EmailStatus
    {
        return new EmailStatus($this->account_status);
    }
}