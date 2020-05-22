<?php

declare(strict_types=1);


namespace App\Form;

use App\Controller\SecurityController;
use App\Form\CustomType\CustomEmailType;
use App\Form\CustomType\UsernameType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class ForgotPasswordType
 * @package App\Form
 */
class ForgotPasswordType extends AbstractType
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
            ]);
    }
}
