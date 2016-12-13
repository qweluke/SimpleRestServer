<?php

namespace CoreBundle\Form\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewUserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'required' => true
            ])
            ->add('firstName', TextType::class, [
                'required' => true
            ])
            ->add('lastName', TextType::class, [
                'required' => true
            ])
            ->add('gender', ChoiceType::class, [
                'required' => true,
                'choices' => array('male', 'female'),
            ])
            ->add('birthDate', DateType::class, [
                'required' => false,
            ])
            ->add('roles', ChoiceType::class, [
                'required' => true,
                'multiple' => true,
                'choices' => array('ROLE_ADMIN' => 'admin', 'ROLE_USER' => 'user'),
            ])
            ->add('email', EmailType::class, [
                'required' => true
            ])
            ->add('plainPassword', TextType::class, [
                'required' => true
            ])
            ->add('enabled', ChoiceType::class, [
                'description' => 'Set 1 for true, 0 for false',
                'choices' => array('1','0'),
                'required' => true,
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
