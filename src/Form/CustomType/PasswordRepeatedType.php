<?php


namespace App\Form\CustomType;


use App\Controller\SecurityController;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Regex;

class PasswordRepeatedType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'mapped' => false,
            'type' => PasswordType::class,
            'invalid_message' => 'Both password fields must be identical',
            'first_name' => SecurityController::PASSWORD_FIELD,
            'first_options' => ['label' => 'Password'],
            'second_name' => SecurityController::PASSWORD_CHECK_FIELD,
            'second_options' => ['label' => 'Re-type password'],
            'options' => [
                'mapped' => 'false',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 12,
                    ]),
                ],
            ]
        ]);
    }

    public function getParent()
    {
        return RepeatedType::class;
    }
}