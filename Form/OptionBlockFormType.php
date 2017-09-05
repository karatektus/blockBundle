<?php
/**
 * Created by PhpStorm.
 * User: pluetzner
 * Date: 14.06.2016
 * Time: 10:34
 */

namespace Pluetzner\BlockBundle\Form;


use Pluetzner\BlockBundle\Entity\OptionBlock;
use Simettric\DoctrineTranslatableFormBundle\Form\TranslatableTextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class StringBlockFormType
 *
 * @package Pluetzner/BlockBundle/Form
 */
class OptionBlockFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (true === $options['save_button']) {
            $builder
                ->add("slug", TextType::class, [
                    "label" => "Abkürzung:",
                    "required" => true
                ]);
        }
            $builder
                ->add('value', ChoiceType::class, [
                    'choices' => $options['choices']
                ]);

        if (true === $options['save_button']) {
            $builder->add('Speichern', SubmitType::class);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => OptionBlock::class,
            'save_button' => false,
            'choices' => [],
            'choices_as_values' => true,
        ]);
    }


}