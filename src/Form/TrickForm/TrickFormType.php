<?php

namespace App\Form\TrickForm;

use App\Entity\Trick;
use App\Form\CustomType\TinyMceTextAreaType;
use App\Form\CustomType\TrickGroupSelectType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrickFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description', TinyMceTextAreaType::class)
            ->add('trickGroup', TrickGroupSelectType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
