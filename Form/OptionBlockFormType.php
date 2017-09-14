<?php
/**
 * Created by PhpStorm.
 * User: pluetzner
 * Date: 14.06.2016
 * Time: 10:34
 */

namespace Pluetzner\BlockBundle\Form;


use Pluetzner\BlockBundle\Entity\OptionBlock;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
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
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options) {
                $this->createForm($event->getForm(), $event->getData(), $options['save_button']);
            });

    }

    /**
     * @param FormInterface $builder
     * @param OptionBlock   $optionBlock
     * @param boolean       $savebutton
     */
    private function createForm($builder, $optionBlock, $savebutton)
    {
        if (true === $savebutton) {
            $builder
                ->add("slug", TextType::class, [
                    "label" => "Abkürzung:",
                    "required" => true
                ]);
        }
        $builder
            ->add('value', ChoiceType::class, [
                'label' => $optionBlock->getTitle(),
                'choices' => $optionBlock->getOptions(),
            ]);

        if (true === $savebutton) {
            $builder->add('Speichern', SubmitType::class);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => OptionBlock::class,
            'save_button' => false,
            'choices_as_values' => true,
        ]);
    }


}