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
 *
 */

declare(strict_types=1);

namespace BaksDev\Ozon\UseCase\Admin\NewEdit;

use BaksDev\DeliveryTransport\Type\ProductParameter\Weight\Kilogram\Kilogram;
use BaksDev\Ozon\UseCase\Admin\NewEdit\Active\OzonTokenActiveForm;
use BaksDev\Ozon\UseCase\Admin\NewEdit\Card\OzonTokenCardForm;
use BaksDev\Ozon\UseCase\Admin\NewEdit\Client\OzonTokenClientForm;
use BaksDev\Ozon\UseCase\Admin\NewEdit\Commission\OzonTokenCommissionForm;
use BaksDev\Ozon\UseCase\Admin\NewEdit\Name\OzonTokenNameForm;
use BaksDev\Ozon\UseCase\Admin\NewEdit\Orders\OzonTokenOrdersForm;
use BaksDev\Ozon\UseCase\Admin\NewEdit\Percent\OzonTokenPercentForm;
use BaksDev\Ozon\UseCase\Admin\NewEdit\Profile\OzonTokenProfileForm;
use BaksDev\Ozon\UseCase\Admin\NewEdit\Sales\OzonTokenSalesForm;
use BaksDev\Ozon\UseCase\Admin\NewEdit\Stocks\OzonTokenStocksForm;
use BaksDev\Ozon\UseCase\Admin\NewEdit\Value\OzonTokenValueForm;
use BaksDev\Ozon\UseCase\Admin\NewEdit\Vat\OzonTokenVatForm;
use BaksDev\Ozon\UseCase\Admin\NewEdit\Warehouse\OzonTokenWarehouseForm;
use BaksDev\Users\Profile\UserProfile\Repository\UserProfileChoice\UserProfileChoiceInterface;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class OzonTokenForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name', OzonTokenNameForm::class, ['label' => false]);

        $builder->add('profile', OzonTokenProfileForm::class, ['label' => false]);

        $builder->add('active', OzonTokenActiveForm::class, ['label' => false]);

        $builder->add('card', OzonTokenCardForm::class, ['label' => false]);

        $builder->add('stocks', OzonTokenStocksForm::class, ['label' => false]);

        $builder->add('sales', OzonTokenSalesForm::class, ['label' => false]);

        $builder->add('orders', OzonTokenOrdersForm::class, ['label' => false]);

        $builder->add('client', OzonTokenClientForm::class, ['label' => false]);

        $builder->add('type', Type\OzonTokenTypeForm::class, ['label' => false]);

        $builder->add('percent', OzonTokenPercentForm::class, ['label' => false]);

        $builder->add('token', OzonTokenValueForm::class, ['label' => false]);

        $builder->add('warehouse', OzonTokenWarehouseForm::class, ['label' => false]);

        $builder->add('vat', OzonTokenVatForm::class, ['label' => false]);

        $builder->add('commission', OzonTokenCommissionForm::class, ['label' => false]);


        /* Сохранить ******************************************************/
        $builder->add(
            'ozon_token',
            SubmitType::class,
            ['label' => 'Save', 'label_html' => true, 'attr' => ['class' => 'btn-primary']],
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OzonTokenDTO::class,
            'method' => 'POST',
            'attr' => ['class' => 'w-100'],
        ]);
    }
}
