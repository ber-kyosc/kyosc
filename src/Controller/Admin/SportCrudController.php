<?php

namespace App\Controller\Admin;

use App\Entity\Sport;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ColorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class SportCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Sport::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setPageTitle('index', 'Sports')
            ->setPageTitle('new', 'Sports');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->setLabel('n°')
                ->hideOnForm(),
            TextField::new('name')
                ->setLabel('Nom'),
            SlugField::new('slug')
                ->setTargetFieldName('name'),
            ImageField::new('logo')
                ->setBasePath('/uploads/logo')
                ->setUploadDir('public/uploads/logo'),
            ImageField::new('picto')
                ->setBasePath('/uploads/picto')
                ->setUploadDir('public/uploads/picto'),
            ImageField::new('goutte')
                ->setBasePath('/uploads/goutte')
                ->setUploadDir('public/uploads/goutte'),
            ColorField::new('color')
                ->setLabel('Couleur'),
            AssociationField::new('category')
                ->setLabel('Catégorie'),
        ];
    }
}
