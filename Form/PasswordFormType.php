<?php

namespace Pluetzner\BlockBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $roles = array('User' => 'ROLE_USER', 'Manager' => 'ROLE_MANAGER');
        if (true === $builder->getData()->hasRole("ROLE_ADMIN")) {
            $roles['Admin'] = 'ROLE_ADMIN';
        }

        $builder->add("plainPassword", RepeatedType::class, [
            'type' => PasswordType::class,
            'invalid_message' => 'Please enter the same password twice.',
            'options' => array('attr' => array('class' => 'password-field')),
            'required' => true,
            'first_options' => array('label' => 'Password'),
            'second_options' => array('label' => 'Repeat'),
        ])
            ->add("save", SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {

    }
}
