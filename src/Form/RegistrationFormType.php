<?php

declare(strict_types=1);

namespace App\Form;

use App\Controller\SecurityController;
use App\Entity\User;
use App\Form\CustomType\CustomEmailType;
use App\Form\CustomType\PasswordRepeatedType;
use App\Form\CustomType\UsernameType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class RegistrationFormType
 * @package App\Form
 */
class RegistrationFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(SecurityController::USERNAME_FIELD, UsernameType::class, [
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add(SecurityController::EMAIL_FIELD, CustomEmailType::class, [
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('passwordGroup', PasswordRepeatedType::class, [
                'constraints' => [
                    new NotBlank()
                ]
            ]);
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
