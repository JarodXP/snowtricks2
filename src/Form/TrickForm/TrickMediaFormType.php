<?php

namespace App\Form\TrickForm;

use App\Entity\Media;
use App\Form\CustomType\MediaImageType;
use App\Form\CustomType\MediaVideoType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrickMediaFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('video', MediaVideoType::class, [
                'label' => 'Upload Video',
                'required' => false
                ])
            ->add('image', MediaImageType::class, [
                'label' => 'Upload Image',
                'required' => false
                ])
            ->add('alt', TextType::class, [
                'label' => 'Alternative text',
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Media::class,
        ]);
    }
}
