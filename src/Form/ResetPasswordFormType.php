<?php

declare(strict_types=1);


namespace App\Form;

use App\Controller\SecurityController;
use App\Form\CustomType\PasswordRepeatedType;
use App\Form\CustomType\UsernameType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class ResetPasswordFormType
 * @package App\Form
 */
class ResetPasswordFormType extends AbstractType
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
            ->add('passwordGroup', PasswordRepeatedType::class, [
                'constraints' => [
                    new NotBlank()
                ]
            ]);
    }
}
