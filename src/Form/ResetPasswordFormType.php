<?php


namespace App\Form;


use App\Controller\SecurityController;
use App\Entity\User;
use App\Form\CustomType\PasswordRepeatedType;
use App\Form\CustomType\UsernameType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResetPasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(SecurityController::USERNAME_FIELD,UsernameType::class)
            ->add('passwordGroup', PasswordRepeatedType::class);
            //->add('resetToken', HiddenType::class);
    }
}