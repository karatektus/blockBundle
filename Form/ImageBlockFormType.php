<?php
/**
 * Created by PhpStorm.
 * User: pluetzner
 * Date: 14.06.2016
 * Time: 10:34
 */

namespace Pluetzner\BlockBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class ImageBlockFormType
 *
 * @package Pluetzner\BlockBundle/Form
 */
class ImageBlockFormType extends AbstractType
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

        $builder->add("uploadedFile", FileType::class, [
            "label" => "Bild",
            "constraints" => [new NotBlank(),
                new File([
                    "mimeTypes" => [
                        "image/gif",
                        "image/png",
                        "image/jpeg",
                    ],
                    "mimeTypesMessage" => "Bitte laden sie eine Bilddatei hoch.",
                    "maxSize" => "2M",
                ])]
        ]);
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