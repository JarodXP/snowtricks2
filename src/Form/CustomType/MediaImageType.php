<?php


namespace App\Form\CustomType;

use App\Entity\Media;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Image;

class MediaImageType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => 'Image (JPG, PNG)',
            'mapped' => false,
            'constraints' => [
                new File([
                    'maxSize' => '1024k',
                    'mimeTypes' => [
                        'image/jpeg',
                        'image/png',
                    ],
                    'mimeTypesMessage' => 'Only PNG and JPG allowed.',
                    'maxSizeMessage' => 'File size must be less than 1Mo'
                ]),
                new Image(['detectCorrupted' => true])
            ],
            'data_class' => Media::class,
        ]);
    }

    public function getParent()
    {
        return FileType::class;
    }
}
