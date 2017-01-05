<?php
/**
 * Created by PhpStorm.
 * User: Lukasz Malicki
 * Date: 1/5/17
 * Time: 7:29 AM
 */

namespace CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class GenderType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => array('male', 'female')
        ));
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}