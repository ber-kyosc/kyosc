<?php

namespace App\Form;

use App\Entity\Picture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class PictureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pathFile', VichFileType::class, [
                'required' => true,
                'label' => false,
                'download_label' => false,
                'attr' => [
                    'placeholder' => 'Choisir une image',
                ],
                'help' => 'Ajoutez une nouvelle photo !',
                'download_uri' => true, // not mandatory, default is true
                'asset_helper' => true,
            ])
            ->add('save-picture', SubmitType::class, [
                'label' => 'Partager'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Picture::class,
        ]);
    }
}
