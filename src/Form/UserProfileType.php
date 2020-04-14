<?php

namespace App\Form;

use App\Entity\User;
use App\Form\CustomType\AvatarType;
use App\Form\CustomType\CustomEmailType;
use App\Form\CustomType\NameType;
use App\Form\CustomType\UsernameType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', UsernameType::class)
            ->add('avatar', AvatarType::class)
            ->add('firstName', NameType::class)
            ->add('lastName', NameType::class)
            ->add('email', CustomEmailType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
