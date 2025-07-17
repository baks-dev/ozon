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
use BaksDev\Ozon\Type\Id\OzonTokenUid;
use BaksDev\Ozon\UseCase\Admin\NewEdit\OzonTokenDTO;
use BaksDev\Ozon\UseCase\Admin\NewEdit\OzonTokenHandler;
use BaksDev\Ozon\UseCase\Admin\NewEdit\Warehouse\OzonTokenWarehouseDTO;
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
            ->findOneBy(['id' => OzonTokenUid::TEST]);

        if($main)
        {
            $em->remove($main);
        }

        $event = $em->getRepository(OzonTokenEvent::class)
            ->findBy(['main' => OzonTokenUid::TEST]);

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

        $OzonTokenDTO->getActive()->setValue(true);
        self::assertTrue($OzonTokenDTO->getActive()->getValue());

        $OzonTokenDTO->getName()->setValue('Название');
        self::assertEquals('Название', $OzonTokenDTO->getName()->getValue());

        $OzonTokenDTO->getClient()->setValue('1234567890');
        self::assertEquals('1234567890', $OzonTokenDTO->getClient()->getValue());

        $UserProfileUid = new UserProfileUid(UserProfileUid::TEST);
        $OzonTokenDTO->getProfile()->setValue($UserProfileUid);
        self::assertSame($UserProfileUid, $OzonTokenDTO->getProfile()->getValue());

        $OzonTokenDTO->getType()->setValue(true);
        self::assertTrue($OzonTokenDTO->getType()->getValue());

        $OzonTokenDTO->getToken()->setValue('ozon_token');
        self::assertEquals('ozon_token', $OzonTokenDTO->getToken()->getValue());


        $EditOzonTokenWarehouseDTO = new OzonTokenWarehouseDTO();

        $EditOzonTokenWarehouseDTO->getValue()->setValue('123456789');
        self::assertEquals('123456789', $EditOzonTokenWarehouseDTO->getValue()->getValue());

        $EditOzonTokenWarehouseDTO->getStocks()->setValue(true);
        self::assertTrue($EditOzonTokenWarehouseDTO->getStocks()->getValue());

        $EditOzonTokenWarehouseDTO->getPrice()->setValue(true);
        self::assertTrue($EditOzonTokenWarehouseDTO->getPrice()->getValue());

        $OzonTokenDTO->addWarehouse($EditOzonTokenWarehouseDTO);


        $OzonTokenDTO->getPercent()->setValue('10');
        self::assertEquals('10', $OzonTokenDTO->getPercent()->getValue());


        /** @var OzonTokenHandler $OzonTokenHandler */
        $OzonTokenHandler = self::getContainer()->get(OzonTokenHandler::class);
        $handle = $OzonTokenHandler->handle($OzonTokenDTO);

        self::assertTrue(($handle instanceof OzonToken), $handle.': Ошибка OzonToken');

    }
}
