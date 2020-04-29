<?php

declare(strict_types=1);


namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Type;

/**
 * Class HomeListFormType
 * @package App\Form
 */
class HomeLimitFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('trickGroup', HiddenType::class, [
                'constraints'=>[
                    new Type([
                         'type' => 'digit',
                         'message' => 'The trick group is not valid!'
                             ]),
                    new Positive([
                         'message' => 'The trick group  is not valid!'
                                 ])
                ]
            ])
            ->add('limit', HiddenType::class, [
                'constraints'=>[
                    new Type([
                        'type' => 'digit',
                        'message' => 'The limit type is not valid!'
                             ]),
                    new Positive([
                        'message' => 'The limit is not valid!'
                                 ])
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
            'csrf_field_name' => '_csrf_token',
            'csrf_token_id'   => 'limit',
        ]);
    }
}
