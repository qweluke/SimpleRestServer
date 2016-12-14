<?php

namespace CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class UserType extends AbstractType
{
    private $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

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


        if($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $builder
                ->add('username', TextType::class, [
                    'required' => $data['required']
                ])
                ->add('firstName', TextType::class, [
                    'required' => $data['required']
                ])
                ->add('lastName', TextType::class, [
                    'required' => $data['required']
                ])
                ->add('gender', ChoiceType::class, [
                    'required' => $data['required'],
                    'choices' => array('male', 'female'),
                ])
                ->add('birthDate', DateType::class, [
                    'required' => false,
                ])
                ->add('roles', ChoiceType::class, [
                    'required' => $data['required'],
                    'multiple' => true,
                    'choices' => array('ROLE_ADMIN' => 'admin', 'ROLE_USER' => 'user'),
                ])
                ->add('email', EmailType::class, [
                    'required' => $data['required']
                ])
                ->add('plainPassword', TextType::class, [
                    'required' => $data['required']
                ])
                ->add('enabled', ChoiceType::class, [
                    'description' => 'Set 1 for true, 0 for false',
                    'choices' => array('1', '0'),
                    'required' => $data['required']
                ]);
        } else {
            $builder
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
                    'required' => false
                ])
                ->add('email', EmailType::class, [
                    'required' => false
                ])
                ->add('plainPassword', TextType::class, [
                    'required' => false
                ]);
        }
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
