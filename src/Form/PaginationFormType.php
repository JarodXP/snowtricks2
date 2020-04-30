<?php

declare(strict_types=1);


namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class PaginationFormType
 * @package App\Form
 */
class PaginationFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('limit', ChoiceType::class, [
                'choices' => [
                    '5' => 5,
                    '10' => 10,
                    '25' => 25,
                    '50' => 50
                ],
                'label' => 'Lines per page:'
            ])
            ->add('filter', HiddenType::class, [
                'constraints'=>[
                    new NotBlank(),
                    new Choice($options['filterFieldList'])
                ]
            ])
            ->add('order', HiddenType::class, [
                'constraints'=>[
                    new NotBlank(),
                    new Choice($options['sortFieldList'])
                ]
            ])
            ->add('direction', HiddenType::class, [
                'constraints'=>[
                    new NotBlank(),
                    new Choice(['ASC','DESC'])
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
            'csrf_token_id'   => 'home_list',
            'sortFieldList'=>[],
            'filterFieldList'=>[],
            'attr'=>['id'=>'pagination']
        ]);
    }
}
