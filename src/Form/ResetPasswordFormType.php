<?php


namespace App\Form;

use App\Controller\SecurityController;
use App\Form\CustomType\PasswordRepeatedType;
use App\Form\CustomType\UsernameType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ResetPasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(SecurityController::USERNAME_FIELD, UsernameType::class)
            ->add('passwordGroup', PasswordRepeatedType::class);
    }
}
