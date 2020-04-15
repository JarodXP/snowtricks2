<?php


namespace App\Form;


use App\Controller\SecurityController;
use App\Form\CustomType\CustomEmailType;
use App\Form\CustomType\PasswordRepeatedType;
use App\Form\CustomType\UsernameType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

class ForgotPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(SecurityController::USERNAME_FIELD,UsernameType::class)
            ->add(SecurityController::EMAIL_FIELD, CustomEmailType::class);
    }
}