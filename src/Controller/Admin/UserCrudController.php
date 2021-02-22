<?php

namespace App\Controller\Admin;

use _HumbugBoxd1d863f2278d\Nette\Utils\Image;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AvatarField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }


    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')
            ->setLabel('n°')
            ->hideOnForm();
        yield TextField::new('pseudo');
        yield ImageField::new('profilPhoto')
            ->setLabel('Photo de profil')
            ->setBasePath('/uploads/profils')
            ->setUploadDir('public/uploads/profils');
        yield EmailField::new('email')
            ->setLabel('Mail');
        yield BooleanField::new('isVerified')
            ->setLabel('Vérifié ?');
        yield IntegerField::new('points');
        yield AssociationField::new('challenges')
            ->onlyOnIndex()
            ->setLabel('Participation challenges');
        yield AssociationField::new('favoriteBrands')
            ->setLabel('Marques preférés');
        yield TextEditorField::new('brandSuggestion')
            ->onlyOnIndex();
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
             yield BooleanField::new('isAdmin')
                 ->setLabel('Admin ?')
                 ->hideOnIndex();
        }
    }
}
