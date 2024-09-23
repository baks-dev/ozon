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

use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;

final class OzonAuthorizationToken
{
    /**
     * ID настройки (профиль пользователя)
     */
    private readonly UserProfileUid $profile;

    /**
     * Токен
     */
    private readonly string $token;

    /**
     * Id Клиента
     */
    private readonly string $client;

    /**
     * Торговая наценка
     */
    private int $percent;


    public function __construct(
        UserProfileUid|string $profile,
        string $token,
        string $client,
        int|string $percent = 0
    ) {
        if(is_string($profile))
        {
            $profile = new UserProfileUid($profile);
        }

        $this->profile = $profile;
        $this->token = $token;
        $this->client = $client;
        $this->percent = (int) $percent;
    }


    public function getProfile(): UserProfileUid
    {
        return $this->profile;
    }


    public function getToken(): string
    {
        return $this->token;
    }

    public function getClient(): string
    {
        return $this->client;
    }

    public function getPercent(): int
    {
        return $this->percent;
    }

}
