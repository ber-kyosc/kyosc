<?php

namespace App\Form;

use App\Entity\Brand;
use App\Entity\Sport;
use App\Entity\User;
use App\Repository\BrandRepository;
use App\Repository\SportRepository;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class EditProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('profilPhotoFile', VichFileType::class, [
                'required' => false,
                'delete_label' => 'Supprimer ma photo',
                'attr' => [
                    'placeholder' => 'Choisir une photo de profil ... ',
                    'onchange' => 'loadFile()',
                ],
                'download_label' => false,
                'asset_helper' => true,
            ])
            ->add('firstName', TextType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Prénom',
                ]])
            ->add('lastName', TextType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Nom',
                ]])
            ->add('pseudo', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Pseudo',
                ]])
            ->add('address', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Adresse',
                ]])
            ->add('city', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Ville',
                ]])
            ->add('postalCode', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Code Postal',
                ]])
            ->add('biography', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Biographie',
                ]])
            ->add('city', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Lieu d\'habitation',
                ]])
            ->add('testimony', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Déposez un témoignage sur votre expérience de KYOSC.',
                ]])
            ->add('age', IntegerType::class, [
                'required' => false,
                'invalid_message' => 'Veuillez renseigner uniquement des nombres entiers.',
                'attr' => [
                    'placeholder' => 'Âge',
                ]])
            ->add('favoriteSports', EntityType::class, [
                'required' => false,
                'label' => 'Mes sports préférés',
                'class' => Sport::class,
                'expanded' => true,
                'multiple' => true,
                'choice_label' => 'name',
                'attr' => ['class' => 'sportList'],
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
            ->add('favoriteBrands', EntityType::class, [
                'required' => false,
                'label' => 'Mes marques préférées',
                'class' => Brand::class,
                'choice_label' => 'name',
                'multiple' => true,
                'query_builder' => function (BrandRepository $br) {
                    return $br->createQueryBuilder('b')
                        ->orderBy('b.name', 'ASC');
                },
                'by_reference' => false,
                'help' => 'Quelles sont les marques que vous utilisez lors de vos pratiques sportives ?',
                'attr' => [
                    'class' => 'select-favoriteBrands',
                ],
            ])
            ->add('brandSuggestion', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Suggestions de vos marques de sport préférées',
                    'rows' => 3],
            ])
            ->add('favoriteDestination', TextareaType::class, [
                'required' => false,
                'help' => 'Quelles sont vos destinations de prédilection pour vos pratiques sportives ?',
                'attr' => [
                    'placeholder' => 'Destinations sportives préférées',
                    'rows' => '5',
                    'cols' => '5',
                ]]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
