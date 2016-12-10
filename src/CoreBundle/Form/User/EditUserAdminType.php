<?php

namespace CoreBundle\Form\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTypee;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditUserAdminType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'required' => false
            ])
            ->add('firstName', TextType::class, [
                'required' => false
            ])
            ->add('lastName', TextType::class, [
                'required' => false
            ])
            ->add('gender', ChoiceType::class, [
                'required' => false,
                'choices' => array('male', 'female'),
            ])
            ->add('birthDate', DateType::class, [
                'required' => false,
            ])
            ->add('roles', ChoiceType::class, [
                'required' => false,
                'multiple' => true,
                'choices' => array('ROLE_ADMIN' => 'admin', 'ROLE_USER' => 'user'),
            ])
            ->add('email', EmailType::class, [
                'required' => false
            ])
            ->add('plainPassword', TextType::class, [
                'required' => false
            ])
            ->add('enabled', ChoiceType::class, [
                'description' => 'Set 1 for true, 0 for false',
                'choices' => array('1','0'),
                'required' => false,
            ])
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CoreBundle\Entity\User',
            'allow_extra_fields' => true
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'user';
    }
}
