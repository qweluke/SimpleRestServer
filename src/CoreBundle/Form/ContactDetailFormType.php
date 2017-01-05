<?php

namespace CoreBundle\Form;

use CoreBundle\Entity\ContactDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ContactDetailFormType extends AbstractType
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
            ->add('type', ChoiceType::class, [
                'description' => 'Set 1 for true, 0 for false',
                'choices' => [ContactDetail::TYPE_EMAIL, ContactDetail::TYPE_FAX, ContactDetail::TYPE_MOBILE, ContactDetail::TYPE_PHONE],
                'required' => $data['required']
            ])
            ->add('value', TextType::class, [
                'required' => $data['required'],
            ])
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CoreBundle\Entity\ContactDetail',
            'allow_extra_fields' => true
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'contactDetails';
    }
}
