<?php

declare(strict_types=1);


namespace App\Form;

use App\Form\CustomType\PasswordRepeatedType;
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
            ->add('passwordGroup', PasswordRepeatedType::class, [
                'constraints' => [
                    new NotBlank()
                ]
            ]);
    }
}
