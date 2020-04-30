<?php

declare(strict_types=1);


namespace App\Form;


use App\Form\CustomType\LimitFieldType;
use App\Form\CustomType\TrickGroupSelectType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


/**
 * Class HomeListFormType
 * @package App\Form
 */
class HomeListFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('trickGroup', TrickGroupSelectType::class, [
                'placeholder' => 'All',
                'required' => false
            ])
            ->add('limit', LimitFieldType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_field_name' => '_csrf_token',
            'csrf_token_id'   => 'home_list',
        ]);
    }
}
