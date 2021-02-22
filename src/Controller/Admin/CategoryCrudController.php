<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ColorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->hideOnForm(),
            TextField::new('name'),
            ImageField::new('logo')
                ->setBasePath('/uploads/logo')
                ->setUploadDir('public/uploads/logo'),
            ImageField::new('picto')
                ->setBasePath('/uploads/picto')
                ->setUploadDir('public/uploads/picto'),
            ImageField::new('goutte')
                ->setBasePath('/uploads/goutte')
                ->setUploadDir('public/uploads/goutte'),
            ColorField::new('color'),
            ImageField::new('carousel')
                ->setBasePath('/uploads/carousel')
                ->setUploadDir('public/uploads/carousel')
        ];
    }
}
