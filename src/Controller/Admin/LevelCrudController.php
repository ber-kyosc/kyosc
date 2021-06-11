<?php

namespace App\Controller\Admin;

use App\Entity\Level;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class LevelCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Level::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name')->setLabel('Nom'),
            BooleanField::new('isUserLevel')->setLabel('niveaux utilisateurs ?'),
            AssociationField::new('users')
                ->setLabel('membres KYOSC')
                ->autocomplete(),
            AssociationField::new('challenges')
                ->setLabel('aventures')
                ->autocomplete(),
        ];
    }
}
