<?php

namespace App\Form;

use App\Entity\Sport;
use App\Entity\Challenge;
use App\Repository\SportRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Vich\UploaderBundle\Form\Type\VichFileType;

class ChallengeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sports', EntityType::class, [
                'class' => Sport::class,
                'expanded' => true,
                'multiple' => true,
                'required' => true,
                'choice_label' => 'name',
                'query_builder' => function (SportRepository $er) {
                    return $er->createQueryBuilder('s')
                        ->orderBy('s.name', 'ASC');
                },
                'choice_attr' => function ($sport) {
                    return [
                        'data-img' => $sport->getGoutte(),
                        'category' => $sport->getCategory(),
                    ];
                }
            ])
            ->add('challengePhotoFile', VichFileType::class, [
                'required' => false,
                'download_label' => false,
                'attr' => [
                    'placeholder' => 'Choisir une image',
                ],
                'help' => 'Illustrer votre aventure en sélectionnant une photo',
                'allow_delete'  => true, // not mandatory, default is true
                'download_uri' => true, // not mandatory, default is true
                'asset_helper' => true,
            ])
            ->add('title', TextType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Titre de l\'aventure'
                ]
            ])
            ->add('quotation', TextType::class, [
                'required' => true,
                'help' => 'Incitez les gens à participer à votre aventure en partageant une citation !',
                'label' => 'Citation Qualifiante',
                'attr' => [
                    'placeholder' => 'Citation qualifiante',
                ]])
            ->add('description', TextareaType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Dites-en plus sur votre aventure !',
                    'rows' => 5],
            ])
            ->add('location', TextType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Lieu de départ'],
            ])
            ->add('locationEnd', TextType::class, [
                'required' => false,
                'help' => 'Si différent du lieu de départ',
                'attr' => [
                    'placeholder' => 'Lieu d\'arrivée'],
            ])
            ->add('dateStart', DateType::class, [
                'required' => true,
                'widget' => 'single_text',
                'html5'  => false,
                'format' => 'dd-MM-yyyy',
                'attr' => [
                    'placeholder' => 'Date de l\'aventure'],
            ])
            ->add('journey', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Parcours et grandes étapes de votre aventure',
                    'rows' => 5],
            ])
            ->add('distance', NumberType::class, [
                'required' => false,
                'help' => 'Distance approximative en km',
                'attr' => [
                    'placeholder' => 'Distance du parcours'],
            ])
            ->add('information', TextareaType::class, [
                'required' => true,
                'help' => 'Exemple: heure et lieu de rendez-vous',
                'attr' => [
                    'placeholder' => 'Informations pratiques.',
                    'rows' => 3],
            ])
            ->add('isPublic', CheckboxType::class, [
                'help' => 'possibilité de rejoindre votre aventure',
                'label' => 'ouvert à tous',
                'required' => false,
                'label_attr' => ['class' => 'switch-custom']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Challenge::class,
        ]);
    }
}
