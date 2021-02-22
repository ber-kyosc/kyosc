<?php

namespace App\Form;

use App\Entity\ChallengeSearch;
use App\Entity\Sport;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChallengeSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateStart', TextType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Date du Challenge',

                ],
            ])
            ->add('sport', EntityType::class, [
                'placeholder' => 'Tous les sports',
                'class' => Sport::class,
                'label' => false,
                'choice_label' => 'name',
                'required' => false,
                'choice_value' => function (?Sport $sport) {
                    return $sport ? $sport->getName() : '';
                }
            ])
            ->add('distance', TextType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Distance',
                    'readonly' => true,
                ]
            ])
            ->add('participants', TextType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Nombre de Participants',
                    'readonly' => true,
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ChallengeSearch::class,
            'method' => 'get',
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
