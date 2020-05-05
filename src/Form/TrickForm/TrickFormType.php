<?php

declare(strict_types=1);

namespace App\Form\TrickForm;

use App\Entity\Trick;
use App\Form\CustomType\TinyMceTextAreaType;
use App\Form\CustomType\TrickGroupSelectType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class TrickFormType
 * @package App\Form\TrickForm
 */
class TrickFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('description', TinyMceTextAreaType::class, [
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('trickGroup', TrickGroupSelectType::class, [
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Published' => 1,
                    'Draft' => 0
                ],
                'constraints' => [
                    new NotBlank()
                ]
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
