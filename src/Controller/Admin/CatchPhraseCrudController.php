<?php

namespace App\Controller\Admin;

use App\Entity\CatchPhrase;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;

class CatchPhraseCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return CatchPhrase::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextEditorField::new('content')->setLabel('Phrase'),
        ];
    }
}
