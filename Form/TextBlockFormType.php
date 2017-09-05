<?php
/**
 * Created by PhpStorm.
 * User: pluetzner
 * Date: 14.06.2016
 * Time: 10:34
 */

namespace Pluetzner\BlockBundle\Form;


use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Simettric\DoctrineTranslatableFormBundle\Form\AbstractTranslatableType;
use Simettric\DoctrineTranslatableFormBundle\Form\TranslatableTextareaType;

/**
 * Class TextBlockFormType
 *
 * @package Pluetzner/BlockBundle/Form
 */
class TextBlockFormType extends AbstractTranslatableType
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
        $this->createTranslatableMapper($builder, $options)
            ->add("text", TranslatableTextareaType::class);

        if (true === $options['save_button']) {
            $builder->add('Speichern', SubmitType::class);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Pluetzner\BlockBundle\Entity\TextBlock',
            'save_button' => false,
        ]);
        $this->configureTranslationOptions($resolver);
    }


}