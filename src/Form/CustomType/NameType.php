<?php


namespace App\Form\CustomType;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Regex;

class NameType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'constraints' => [
                new Length([
                    'max' => 30,
                    'maxMessage' => 'Name must be maximum 30 characters long.'
                ]),
                new Regex([
                    'pattern' => '~^[a-zA-Z\-\s]{0,30}$~',
                    'message' => 'the name can only contain letters, spaces and dashes.']),
            ]
        ]);
    }

    public function getParent()
    {
        return TextType::class;
    }
}