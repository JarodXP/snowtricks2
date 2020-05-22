<?php

declare(strict_types=1);


namespace App\Form\CustomType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Type;

/**
 * Class LimitFieldType
 * @package App\Form\CustomType
 */
class LimitFieldType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
           'constraints'=>[
               new Type([
                    'type' => 'digit',
                    'message' => 'The limit type is not valid!'
                ]),
               new Positive([
                    'message' => 'The limit is not valid!'
                ])
           ]
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return HiddenType::class;
    }
}
