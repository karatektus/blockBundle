<?php
/**
 * Created by PhpStorm.
 * User: pluetzner
 * Date: 14.06.2016
 * Time: 10:34
 */

namespace Pluetzner\BlockBundle\Form;

use Pluetzner\BlockBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;


/**
 * Class UserFormType
 *
 * @package PM\CoreBundle\Form
 */
class UserFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var AuthorizationChecker $checker */
        $checker = $options['checker'];

        $roles = array(
            'User' => 'ROLE_USER',
        );
        if (true === $options['user']->hasRole("ROLE_ADMIN_DEVELOPER")) {
            $roles['Admin'] = 'ROLE_ADMIN';
            $roles['Entwickler'] = 'ROLE_ADMIN_DEVELOPER';
        } elseif (true === $options['user']->hasRole("ROLE_ADMIN")) {
            $roles['Admin'] = 'ROLE_ADMIN';
        }



        /** @var User $user */
        $user = $builder->getData();

        if (null === $user->getId()) {
            $required = true;
            $isDisabled = false;
        } else {
            $required = false;
            $isDisabled = true;
        }

        $builder
            ->add("email", TextType::class, [
                "label" => "E-Mail:",
                "required" => true
            ])
            ->add("plainPassword", RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Bitte geben sie zwei mal das gleiche Passwort ein.',
                'options' => array('attr' => array('class' => 'password-field')),
                'first_options' => array('label' => 'Passwort'),
                'second_options' => array('label' => 'Passwort wiederholen'),
                'required' => $required,
            ])
            ->add('function', ChoiceType::class, [
                'label' => 'Berechtigung:',
                'choices' => $roles,
                'choices_as_values' => true,
                'multiple' => false,
            ])
            ->add("speichern", SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(array(
            'checker'
        ));

        $resolver->setAllowedTypes('checker', AuthorizationChecker::class);

        $resolver->setDefaults([
            "user" => null
        ]);
    }


}