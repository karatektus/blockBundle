<?php
/**
 * Created by PhpStorm.
 * User: pluetzner
 * Date: 11.08.2017
 * Time: 16:01
 */

namespace Pluetzner\BlockBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ImportFormType
 *
 * @package Pluetzner\BlockBundle\Form
 */
class ImportLanguageSelectFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("locales", ChoiceType::class, [
                "label" => "Languages",
                'multiple' => true,
                'expanded' => true,
                'choices' => $options['locales'],
                'choices_as_values' => true,
                'required' => true,
            ])
            ->add("Save", SubmitType::class);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired([
            'locales'
        ]);
    }
}