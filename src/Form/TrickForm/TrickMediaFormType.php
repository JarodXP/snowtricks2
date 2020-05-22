<?php

declare(strict_types=1);

namespace App\Form\TrickForm;

use App\Entity\Media;
use App\Form\CustomType\MediaImageType;
use App\Form\CustomType\MediaVideoType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TrickMediaFormType
 * @package App\Form\TrickForm
 */
class TrickMediaFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
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
                ])
            ->add('mainImage', CheckboxType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Use this image as Main Image ?',
                'false_values' => [null]
                ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Media::class,
        ]);
    }
}
