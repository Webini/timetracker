<?php
/**
 * Created by PhpStorm.
 * User: nico
 * Date: 26/05/18
 * Time: 20:10
 */

namespace App\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BooleanType extends ChoiceType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'choices'    => [ 'yes' => true, 'no' => false ],
            'required'   => false,
            'nullable'   => true,
        ]);
        $resolver->setAllowedTypes('nullable', [ 'null', 'boolean' ]);
    }

}