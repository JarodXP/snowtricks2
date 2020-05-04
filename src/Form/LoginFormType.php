<?php

declare(strict_types=1);


namespace App\Form;

use App\Controller\SecurityController;
use App\Form\CustomType\CustomPasswordType;
use App\Form\CustomType\UsernameType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class LoginFormType
 * @package App\Form
 */
class LoginFormType extends AbstractType
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
            ->add(SecurityController::PASSWORD_FIELD, CustomPasswordType::class, [
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
            'csrf_field_name' => '_csrf_token',
            'csrf_token_id'   => 'authenticate',
        ]);
    }
}
