<?php
/**
 * Created by PhpStorm.
 * User: pluetzner
 * Date: 14.06.2016
 * Time: 10:34
 */

namespace Pluetzner\BlockBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class EntityBlockFormType
 *
 * @package Pluetzner/BlockBundle/Form
 */
class EntityBlockFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("title", TextType::class, [
                "label" => "Titel:",
                "required" => true
            ])
            ->add("published", DateType::class, [
                "label" => "Veröffentlichungsdatum:",
                'widget' => 'single_text',
            ])
            ->add('stringBlocks', CollectionType::class, [
                'label' => false,
                'entry_type' => StringBlockFormType::class,
                'entry_options' => ['label' => false],
            ])
            ->add('textBlocks', CollectionType::class, [
                'label' => false,
                'entry_type' => TextBlockFormType::class,
                'entry_options' => ['label' => false],
            ])
            ->add('imageBlocks', CollectionType::class, [
                'label' => false,
                'entry_type' => ImageBlockFormType::class,
                'entry_options' => ['label' => false],
            ])/*
            ->add('optionBlock', CollectionType::class, [
                'label' => false,
                'entry_type' => OptionBlockFormType::class,
                'entry_options' => ['label' => false],
            ])*/;

        if (true === $options['save_button']) {
            $builder->add('Speichern', SubmitType::class);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'save_button' => true,
        ]);
    }


}