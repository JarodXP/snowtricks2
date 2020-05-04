<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\User;
use App\Form\CustomType\CustomEmailType;
use App\Form\CustomType\MediaImageType;
use App\Form\CustomType\NameType;
use App\Form\CustomType\UsernameType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class UserProfileType
 * @package App\Form
 */
class UserProfileType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', UsernameType::class, [
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('avatar', MediaImageType::class, ['required'=>false])
            ->add('firstName', NameType::class, ['required'=>false])
            ->add('lastName', NameType::class, ['required'=>false])
            ->add('email', CustomEmailType::class, [
                'constraints' => [
                    new NotBlank()
                ]
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
