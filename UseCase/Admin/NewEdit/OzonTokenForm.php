<?php

declare(strict_types=1);

namespace BaksDev\Ozon\UseCase\Admin\NewEdit;

use BaksDev\DeliveryTransport\Type\ProductParameter\Weight\Kilogram\Kilogram;
use BaksDev\Users\Profile\UserProfile\Repository\UserProfileChoice\UserProfileChoiceInterface;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class OzonTokenForm extends AbstractType
{
    public function __construct(
        private readonly UserProfileChoiceInterface $profileChoice
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var OzonTokenDTO $data */
        $data = $builder->getData();

        if(!$data->getProfile())
        {
            /* TextType */
            $builder->add('profile', ChoiceType::class, [
                'choices' => $this->profileChoice->getActiveUserProfile(),
                'choice_value' => function (?UserProfileUid $profile) {
                    return $profile?->getValue();
                },
                'choice_label' => function (UserProfileUid $profile) {
                    return $profile->getAttr();
                },
                'label' => false,
                'expanded' => false,
                'multiple' => false,
                'required' => false,
                'attr' => ['data-select' => 'select2',]
            ]);

        }

        $builder->add('active', CheckboxType::class, ['required' => false]);

        $builder->add('client', NumberType::class, ['required' => false]);

        $builder->get('client')->addModelTransformer(
            new CallbackTransformer(
                function ($client) {
                    return (int) $client;
                },
                function ($client) {
                    return $client ? (string) $client : null;
                }
            )
        );

        $builder->add('warehouse', NumberType::class, ['required' => false]);

        $builder->get('warehouse')->addModelTransformer(
            new CallbackTransformer(
                function ($client) {
                    return (int) $client;
                },
                function ($client) {
                    return $client ? (string) $client : null;
                }
            )
        );


        $builder->add('token', TextareaType::class, ['required' => false]);

        $builder->add('percent', TextType::class);

        /* Сохранить ******************************************************/
        $builder->add(
            'ozon_token',
            SubmitType::class,
            ['label' => 'Save', 'label_html' => true, 'attr' => ['class' => 'btn-primary']]
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
