<?php

namespace CoreBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class CompanyFormType extends AbstractType
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
            ->add('name', TextType::class, [
                'required' => $data['required']
            ])
            ->add('description', TextType::class, [
                'required' => false
            ])
            ->add('contacts', EntityType::class, [
                'class' => 'CoreBundle\Entity\Contact',
                'description' => 'Array of contact\'s ID\'s',
                'multiple' => true,
                'required' => false
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CoreBundle\Entity\Company',
            'allow_extra_fields' => true
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'companyForm';
    }
}
