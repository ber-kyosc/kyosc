<?php

namespace App\Controller\Admin;

use App\Entity\Challenge;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ChallengeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Challenge::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->setLabel('n°')
                ->hideOnForm(),
            TextField::new('title')
                ->setLabel('Titre'),
            TextEditorField::new('quotation')
                ->setLabel('Citation')
                ->hideOnIndex(),
            TextEditorField::new('description'),
            TextField::new('location')
                ->setLabel('Départ'),
            DateField::new('dateStart')
                ->setLabel('Date de début')
                ->setFormat('dd/MM/yyyy'),
            IntegerField::new('distance')
                ->hideOnIndex(),
            TextEditorField::new('information')
                ->hideOnIndex(),
            BooleanField::new('isPublic')
                ->setLabel('Statut')
                ->hideOnIndex(),
            BooleanField::new('isFeatured')
                ->setLabel('A la Une'),
            AssociationField::new('sports')
                ->autocomplete(),
            AssociationField::new('creator')
                ->setLabel('Créateur')
                ->autocomplete(),
            AssociationField::new('clans')
                ->autocomplete(),
            TextEditorField::new('recommendation')
                ->hideOnIndex(),
        ];
    }
}
