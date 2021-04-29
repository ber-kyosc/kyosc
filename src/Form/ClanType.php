<?php

namespace App\Form;

use App\Entity\Clan;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class ClanType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Nom du clan'
                ]
            ])
            ->add('bannerFile', VichFileType::class, [
                'required' => false,
                'download_label' => false,
                'attr' => [
                    'placeholder' => 'Choisir une image',
                ],
                'help' => 'Illustrez votre clan en sélectionnant une image',
                'allow_delete'  => true, // not mandatory, default is true
                'download_uri' => true, // not mandatory, default is true
                'asset_helper' => true,
            ])
            ->add('description', TextareaType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Décrivez votre clan',
                    'rows' => 5],
            ])
            ->add('isPublic', CheckboxType::class, [
                'help' => 'Visibilité de votre clan',
                'label' => 'Ouvert',
                'required' => false,
                'label_attr' => ['class' => 'switch-custom']
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Clan::class,
        ]);
    }
}
