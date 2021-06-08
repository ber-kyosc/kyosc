<?php

namespace App\Form;

use App\Entity\Clan;
use App\Entity\Sport;
use App\Entity\Challenge;
use App\Repository\ClanRepository;
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
use Symfony\Component\Security\Core\Security;
use Vich\UploaderBundle\Form\Type\VichFileType;

class ChallengeType extends AbstractType
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

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
                    'onchange' => 'loadFile()',
                ],
                'help' => 'Illustrer votre aventure en sélectionnant une photo',
                'allow_delete'  => true, // not mandatory, default is true
                'download_uri' => true, // not mandatory, default is true
                'asset_helper' => true,
            ])
            ->add('gpxTrackFile', VichFileType::class, [
                'required' => false,
                'download_label' => false,
                'attr' => [
                    'placeholder' => 'Choisir un fichier',
                ],
                'help' => 'Vous pouvez ajouter une trace au format gpx, qui sera téléchargeable par les participants',
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
                'required' => false,
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
            ->add('dateStart', DateType::class, [
                'required' => true,
                'widget' => 'single_text',
                'html5'  => false,
                'format' => 'dd-MM-yyyy',
                'attr' => [
                    'placeholder' => 'Date de l\'aventure'],
            ])
            ->add('distance', NumberType::class, [
                'required' => false,
                'help' => 'Distance approximative en km',
                'attr' => [
                    'placeholder' => 'Distance du parcours'],
            ])
            ->add('information', TextareaType::class, [
                'required' => false,
                'help' => 'Exemple: heure et lieu de rendez-vous',
                'attr' => [
                    'placeholder' => 'Informations pratiques.',
                    'rows' => 3],
            ])
            ->add('recommendation', TextareaType::class, [
                'required' => false,
                'help' => 'Partagez des bons plans en lien avec cette aventure ! 
                (restaurants, lieux touristiques et autres sites d\'intérêt)',
                'attr' => [
                    'placeholder' => 'Bons plans',
                    'rows' => 3],
            ])
            ->add('clans', EntityType::class, [
                'required' => false,
                'label' => 'Partager avec mes clans',
                'class' => Clan::class,
                'choice_label' => 'name',
                'multiple' => true,
                'query_builder' => function (ClanRepository $cr) {
                    return $cr->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC')
                        ->where(':user MEMBER OF c.members')
                        ->setParameter("user", $this->security->getUser());
                },
                'by_reference' => false,
                'help' => 'Avec quels clans souhaitez-vous partager votre aventure',
                'attr' => [
                    'class' => 'select-clans',
                ],
            ])
            ->add('isPublic', CheckboxType::class, [
                'help' => 'si vous sélectionnez « ouverte » n’importe quel membre de KYOSC pourra vous 
                faire une demande par mail de participation pour cette aventure (par défaut elle restera privée)',
                'label' => 'ouverte',
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
