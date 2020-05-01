<?php

declare(strict_types=1);


namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class SimplePaginationFormType
 * Used in simple pagination without choice of sort, limits or filter.
 * @package App\Form
 */
class SimplePaginationFormType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_field_name' => '_csrf_token',
            'csrf_token_id'   => 'simple_pagination',
            'attr'=>['id'=>'pagination']
        ]);
    }
}
