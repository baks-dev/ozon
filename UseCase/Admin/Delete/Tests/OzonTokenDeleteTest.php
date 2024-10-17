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

namespace BaksDev\Ozon\UseCase\Admin\Delete\Tests;

use BaksDev\Ozon\Entity\Event\OzonTokenEvent;
use BaksDev\Ozon\Entity\OzonToken;
use BaksDev\Ozon\Repository\OzonTokenCurrentEvent\OzonTokenCurrentEventInterface;
use BaksDev\Ozon\UseCase\Admin\Delete\OzonTokenDeleteDTO;
use BaksDev\Ozon\UseCase\Admin\Delete\OzonTokenDeleteHandler;
use BaksDev\Ozon\UseCase\Admin\NewEdit\OzonTokenDTO;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

/**
 * @group ozon
 * @group ozon-usecase
 *
 * @depends BaksDev\Ozon\UseCase\Admin\NewEdit\Tests\OzonTokenNewTest::class
 * @depends BaksDev\Ozon\UseCase\Admin\NewEdit\Tests\OzonTokenEditTest::class
 */
#[When(env: 'test')]
class OzonTokenDeleteTest extends KernelTestCase
{
    public function testUseCase(): void
    {
        /** @var OzonTokenCurrentEventInterface $OzonTokenCurrentEvent */
        $OzonTokenCurrentEvent = self::getContainer()->get(OzonTokenCurrentEventInterface::class);
        $OzonTokenEvent = $OzonTokenCurrentEvent->findByProfile(UserProfileUid::TEST);
        self::assertNotNull($OzonTokenEvent);
        self::assertNotFalse($OzonTokenEvent);


        /** @see OzonTokenDTO */
        $OzonTokenDTO = new OzonTokenDTO();
        $OzonTokenEvent->getDto($OzonTokenDTO);

        self::assertEquals('ozon_token_edit', $OzonTokenDTO->getToken());
        self::assertEquals('987654321', $OzonTokenDTO->getClient());
        self::assertEquals('78789878978', $OzonTokenDTO->getWarehouse());
        self::assertFalse($OzonTokenDTO->getActive());
        self::assertFalse($OzonTokenDTO->getProfile()->equals(UserProfileUid::TEST)); //($UserProfileUid::TEST, $OzonTokenDTO->getProfile());

        /** @see OzonTokenDeleteDTO */
        $OzonTokenDeleteDTO = new OzonTokenDeleteDTO();
        $OzonTokenEvent->getDto($OzonTokenDeleteDTO);

        /** @var OzonTokenDeleteHandler $OzonTokenHandler */
        $OzonTokenDeleteHandler = self::getContainer()->get(OzonTokenDeleteHandler::class);
        $handle = $OzonTokenDeleteHandler->handle($OzonTokenDeleteDTO);

        self::assertTrue(($handle instanceof OzonToken), $handle.': Ошибка OzonToken');

    }

    public static function tearDownAfterClass(): void
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
}
