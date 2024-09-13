<?php

declare(strict_types=1);

namespace BaksDev\Ozon\UseCase\Admin\Delete;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class OzonTokenDeleteForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /* Сохранить ******************************************************/
        $builder->add(
            'ozon_token_delete',
            SubmitType::class,
            ['label' => 'Save', 'label_html' => true, 'attr' => ['class' => 'btn-primary']]
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OzonTokenDeleteDTO::class,
            'method' => 'POST',
            'attr' => ['class' => 'w-100'],
        ]);
    }
}
