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

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use BaksDev\Ozon\BaksDevOzonBundle;
use BaksDev\Ozon\Type\Event\OzonTokenEventType;
use BaksDev\Ozon\Type\Event\OzonTokenEventUid;
use BaksDev\Ozon\Type\Id\OzonTokenType;
use BaksDev\Ozon\Type\Id\OzonTokenUid;
use BaksDev\Ozon\Type\Warehouse\OzonTokenWarehouseType;
use BaksDev\Ozon\Type\Warehouse\OzonTokenWarehouseUid;
use Symfony\Config\DoctrineConfig;

return static function (DoctrineConfig $doctrine, ContainerConfigurator $configurator): void {


    $doctrine->dbal()->type(OzonTokenUid::TYPE)->class(OzonTokenType::class);
    $doctrine->dbal()->type(OzonTokenEventUid::TYPE)->class(OzonTokenEventType::class);
    $doctrine->dbal()->type(OzonTokenWarehouseUid::TYPE)->class(OzonTokenWarehouseType::class);

    $emDefault = $doctrine->orm()->entityManager('default')->autoMapping(true);

    $emDefault
        ->mapping('ozon')
        ->type('attribute')
        ->dir(BaksDevOzonBundle::PATH.'Entity')
        ->isBundle(false)
        ->prefix('BaksDev\Ozon\Entity')
        ->alias('ozon');
};
