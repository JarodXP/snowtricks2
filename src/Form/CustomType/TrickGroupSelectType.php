<?php


namespace App\Form\CustomType;

use App\Entity\TrickGroup;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrickGroupSelectType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => TrickGroup::class,
            'choice_label' => function ($trickGroup) {
                return $trickGroup->getName();
            },

        ]);
    }

    public function getParent()
    {
        return EntityType::class;
    }
}
