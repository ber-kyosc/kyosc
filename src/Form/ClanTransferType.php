<?php

namespace App\Form;

use App\Entity\Clan;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class ClanTransferType extends AbstractType
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
            ->add('creator', EntityType::class, [
                'class' => User::class,
                'required' => true,
                'choice_label' => 'fullName',
                'query_builder' => function (UserRepository $userRepository) {
                    return $userRepository->createQueryBuilder('u')
                        ->orderBy('u.firstName', 'ASC');
                },
                'attr' => [
                    'class' => 'select2'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Clan::class,
        ]);
    }
}
