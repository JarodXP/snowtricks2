<?php


namespace App\Form\CustomType;

use App\Entity\Media;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class MediaVideoType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => 'Video (MP4 only)',
            'mapped' => false,
            'constraints' => [
                new File([
                    'maxSize' => '20000k',
                    'mimeTypes' => [
                        'video/mp4',
                    ],
                    'mimeTypesMessage' => 'Only MP4 are allowed.',
                    'maxSizeMessage' => 'File size must be less than 20Mo',
                    'uploadPartialErrorMessage' => true
                ]),
            ],
            'data_class' => Media::class,
        ]);
    }

    public function getParent()
    {
        return FileType::class;
    }
}
