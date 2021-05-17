<?php

namespace App\Controller\Admin;

use App\Entity\Clan;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ClanCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Clan::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->setLabel('n°')
                ->hideOnForm(),
            TextField::new('name')
                ->setLabel('Nom'),
            TextEditorField::new('description'),
            BooleanField::new('isPublic')
                ->setLabel('Statut')
                ->hideOnIndex(),
            AssociationField::new('challenges')
                ->setLabel('nombre aventures')
                ->autocomplete(),
            AssociationField::new('creator')
                ->setLabel('Créateur')
                ->autocomplete(),
            DateField::new('createdAt')
                ->setLabel('Date de création')
                ->setFormat('dd/MM/yyyy'),
        ];
    }
}
