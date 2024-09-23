<?php
/*
 *  Copyright 2024.  Baks.dev <admin@baks.dev>
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

namespace BaksDev\Ozon\UseCase\Admin\NewEdit\Tests;

use BaksDev\Ozon\Entity\Event\OzonTokenEvent;
use BaksDev\Ozon\Entity\OzonToken;
use BaksDev\Ozon\UseCase\Admin\NewEdit\OzonTokenDTO;
use BaksDev\Ozon\UseCase\Admin\NewEdit\OzonTokenHandler;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

/**
 * @group ozon
 * @group ozon-usecase
 */
#[When(env: 'test')]
class OzonTokenNewTest extends KernelTestCase
{
    public static function setUpBeforeClass(): void
    {
        /** @var EntityManagerInterface $em */
        $em = self::getContainer()->get(EntityManagerInterface::class);

        $main = $em->getRepository(OzonToken::class)
            ->findOneBy(['id' => UserProfileUid::TEST]);

        if($main)
        {
            $em->remove($main);
        }

        $event = $em->getRepository(OzonTokenEvent::class)
            ->findBy(['profile' => UserProfileUid::TEST]);

        foreach($event as $remove)
        {
            $em->remove($remove);
        }

        $em->flush();
        $em->clear();
    }


    public function testUseCase(): void
    {
        /** @see OzonTokenDTO */
        $OzonTokenDTO = new OzonTokenDTO();

        $OzonTokenDTO->setToken('ozon_token');
        $OzonTokenDTO->setClient('ozon_client_id');

        self::assertEquals('ozon_token', $OzonTokenDTO->getToken());
        self::assertEquals('ozon_client_id', $OzonTokenDTO->getClient());


        $OzonTokenDTO->setActive(true);
        self::assertTrue($OzonTokenDTO->getActive());

        $UserProfileUid = new UserProfileUid(UserProfileUid::TEST);
        $OzonTokenDTO->setProfile($UserProfileUid);
        self::assertSame($UserProfileUid, $OzonTokenDTO->getProfile());

        /** @var OzonTokenHandler $OzonTokenHandler */
        $OzonTokenHandler = self::getContainer()->get(OzonTokenHandler::class);
        $handle = $OzonTokenHandler->handle($OzonTokenDTO);

        self::assertTrue(($handle instanceof OzonToken), $handle.': Ошибка OzonToken');

    }
}
