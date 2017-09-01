<?php
/**
 * Created by PhpStorm.
 * User: pluetzner
 * Date: 11.08.2017
 * Time: 16:01
 */

namespace Pluetzner\BlockBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class ImportFormType
 *
 * @package Pluetzner\BlockBundle\Form
 */
class ImportFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("uploadedFile", FileType::class, [
                "label" => " ",
                "constraints" => [
                    new File(
                    ),
                    new NotBlank(),
                ]
            ])
            ->add("Save", SubmitType::class);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
    }
}