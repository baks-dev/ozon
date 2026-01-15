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

namespace BaksDev\Ozon\Repository\AllOzonToken\Tests;

use BaksDev\Ozon\Repository\AllOzonToken\AllOzonTokenInterface;
use BaksDev\Ozon\Repository\AllOzonToken\OzonTokenPaginatorResult;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use Generator;
use PHPUnit\Framework\Attributes\DependsOnClass;
use PHPUnit\Framework\Attributes\Group;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\DependencyInjection\Attribute\When;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;


#[Group('ozon')]
#[When(env: 'test')]
class AllOzonTokenRepositoryTest extends KernelTestCase
{
    public function testAllOzonTokenRepository(): void
    {
        // Бросаем событие консольной комманды
        $dispatcher = self::getContainer()->get(EventDispatcherInterface::class);
        $event = new ConsoleCommandEvent(new Command(), new StringInput(''), new NullOutput());
        $dispatcher->dispatch($event, 'console.command');

        self::assertTrue(true);

        /** @var AllOzonTokenInterface $AllOzonTokenRepository */
        $AllOzonTokenRepository = self::getContainer()->get(AllOzonTokenInterface::class);

        /** @var Generator|false $result */
        $result = $AllOzonTokenRepository
            ->profile(new UserProfileUid(UserProfileUid::TEST))
            ->findAllPaginator()
            ->getData();

        if(empty($result))
        {
            return;
        }

        foreach($result as $AllOzonTokenResult)
        {
            // Вызываем все геттеры
            $reflectionClass = new ReflectionClass(OzonTokenPaginatorResult::class);
            $methods = $reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC);

            foreach($methods as $method)
            {
                // Методы без аргументов
                if($method->getNumberOfParameters() === 0)
                {
                    // Вызываем метод
                    $data = $method->invoke($AllOzonTokenResult);
                    dump($data);
                }
            }

            break;
        }

    }

}