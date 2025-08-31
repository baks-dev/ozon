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

use BaksDev\Ozon\Entity\OzonToken;
use BaksDev\Ozon\Repository\OzonTokenCurrentEvent\OzonTokenCurrentEventInterface;
use BaksDev\Ozon\Type\Id\OzonTokenUid;
use BaksDev\Ozon\UseCase\Admin\NewEdit\OzonTokenDTO;
use BaksDev\Ozon\UseCase\Admin\NewEdit\OzonTokenHandler;
use BaksDev\Ozon\UseCase\Admin\NewEdit\Warehouse\OzonTokenWarehouseDTO;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use PHPUnit\Framework\Attributes\DependsOnClass;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

#[Group('ozon')]
#[When(env: 'test')]
class OzonTokenEditTest extends KernelTestCase
{
    #[DependsOnClass(OzonTokenNewTest::class)]
    public function testUseCase(): void
    {
        /** @var OzonTokenCurrentEventInterface $OzonTokenCurrentEvent */
        $OzonTokenCurrentEvent = self::getContainer()->get(OzonTokenCurrentEventInterface::class);
        $OzonTokenEvent = $OzonTokenCurrentEvent
            ->find(new OzonTokenUid(OzonTokenUid::TEST));

        self::assertNotFalse($OzonTokenEvent);
        self::assertNotNull($OzonTokenEvent);

        /** @see OzonTokenDTO */
        $OzonTokenDTO = new OzonTokenDTO();
        $OzonTokenEvent->getDto($OzonTokenDTO);


        self::assertTrue($OzonTokenDTO->getActive()->getValue());
        $OzonTokenDTO->getActive()->setValue(false);

        self::assertEquals('Название', $OzonTokenDTO->getName()->getValue());
        $OzonTokenDTO->getName()->setValue('Новое название');

        self::assertEquals('1234567890', $OzonTokenDTO->getClient()->getValue());
        $OzonTokenDTO->getClient()->setValue('987654321');

        self::assertEquals('10', $OzonTokenDTO->getPercent()->getValue());
        $OzonTokenDTO->getPercent()->setValue('-10');

        self::assertTrue($OzonTokenDTO->getProfile()->getValue()->equals(UserProfileUid::TEST));
        $UserProfileUid = new UserProfileUid();
        $OzonTokenDTO->getProfile()->setValue(clone $UserProfileUid);

        self::assertTrue($OzonTokenDTO->getType()->getValue());
        $OzonTokenDTO->getType()->setValue(false);

        self::assertEquals('ozon_token', $OzonTokenDTO->getToken()->getValue());
        $OzonTokenDTO->getToken()->setValue('ozon_token_edit');


        $EditOzonTokenWarehouseDTO = $OzonTokenDTO->getWarehouse()->current();
        self::assertNotFalse($EditOzonTokenWarehouseDTO);
        self::assertCount(1, $OzonTokenDTO->getWarehouse());
        self::assertInstanceOf(OzonTokenWarehouseDTO::class, $EditOzonTokenWarehouseDTO);


        self::assertEquals('123456789', $EditOzonTokenWarehouseDTO->getValue()->getValue());
        $EditOzonTokenWarehouseDTO->getValue()->setValue('78789878978');

        self::assertTrue($EditOzonTokenWarehouseDTO->getPrice()->getValue());
        $EditOzonTokenWarehouseDTO->getPrice()->setValue(false);

        self::assertTrue($EditOzonTokenWarehouseDTO->getStocks()->getValue());
        $EditOzonTokenWarehouseDTO->getStocks()->setValue(false);


        /** @var OzonTokenHandler $OzonTokenHandler */
        $OzonTokenHandler = self::getContainer()->get(OzonTokenHandler::class);
        $handle = $OzonTokenHandler->handle($OzonTokenDTO);

        self::assertTrue(($handle instanceof OzonToken), $handle.': Ошибка OzonToken');

    }
}
