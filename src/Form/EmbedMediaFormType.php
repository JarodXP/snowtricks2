<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\EmbedMedia;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TrickMediaFormType
 * @package App\Form\TrickForm
 */
class EmbedMediaFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('htmlCode', TextareaType::class, [
                'label' => 'HTML Code'
                ])
            ->add('mediaName', TextType::class, [
                'label' => 'Video title',
                'required' => false
                ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EmbedMedia::class,
        ]);
    }
}
