<?php
/*
 *  Copyright 2025.  Baks.dev <admin@baks.dev>
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

namespace BaksDev\Ozon\Listeners\Entity;


use BaksDev\Core\Type\Ip\IpAddress;
use BaksDev\Ozon\Entity\Event\OzonTokenEvent;
use BaksDev\Users\User\Repository\UserTokenStorage\UserTokenStorageInterface;
use BaksDev\Yandex\Market\Entity\Event\YaMarketTokenEvent;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\RequestStack;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: OzonTokenEvent::class)]
final readonly class OzonTokenModifyListener
{
    public function __construct(
        private RequestStack $request,
        private UserTokenStorageInterface $token,
    ) {}

    public function prePersist(OzonTokenEvent $data, LifecycleEventArgs $event): void
    {
        if($this->token->isUser())
        {
            $data->getUser()->setValue($this->token->getUserCurrent());
        }

        /* Если пользователь не из консоли */
        if($this->request->getCurrentRequest())
        {
            $data->getIpAddress()->setValue(new IpAddress($this->request->getCurrentRequest()->getClientIp()));
            $data->getAgent()->setValue($this->request->getCurrentRequest()->headers->get('User-Agent'));
        }
    }
}