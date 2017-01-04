<?php

namespace CoreBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ContactFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $data = [];
        $data['required'] = false;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $user = $event->getData();
//            $form = $event->getForm();

            if (!$user || null === $user->getId()) {
                $data['required'] = true;
            }
        });

        $builder
            ->add('firstName', TextType::class, [
                'required' => $data['required']
            ])
            ->add('lastName', TextType::class, [
                'required' => $data['required']
            ])
            ->add('jobTitle', TextType::class, [
                'required' => $data['required']
            ])
            ->add('company', EntityType::class, [
                'class' => 'CoreBundle\Entity\Company',
                'multiple' => false,
                'required' => $data['required']
            ])
            ->add('image', FileType::class, [
                'required' => false
            ])
            ->add('birthDate', DateType::class, [
                'required' => $data['required'],
                'format' => 'yyyy-MM-dd',
                'widget' => 'single_text',
            ])
            ->add('editableAll', ChoiceType::class, [
                'description' => 'Set 1 for true, 0 for false',
                'choices' => array('1', '0'),
                'required' => $data['required']
            ])
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CoreBundle\Entity\Contact',
            'allow_extra_fields' => true
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'contact';
    }
}
