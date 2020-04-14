<?php


namespace App\Form\CustomType;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Regex;

class UsernameType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'attr'=>['data' => '{{ last_username }}'],
            'constraints' => [
                new NotNull(['message' => 'Username can\'t be empty.']),
                new Length([
                    'min' => 3,
                    'max' => 12,
                    'minMessage' => 'Username must be minimum 3 characters long.',
                    'maxMessage' => 'Username must be maximum 12 characters long.'
                ]),
                new Regex([
                    'pattern' => '~[a-zA-Z0-9\-\_]{3,12}~',
                    'message' => 'Pb REGEX']),
            ]
        ]);
    }

    public function getParent()
    {
        return TextType::class;
    }
}