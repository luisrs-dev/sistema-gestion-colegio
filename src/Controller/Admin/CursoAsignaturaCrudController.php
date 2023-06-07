<?php

namespace App\Controller\Admin;

use App\Entity\CursoAsignatura;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;

class CursoAsignaturaCrudController extends AbstractCrudController
{

    private EntityManagerInterface $em;
    
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public static function getEntityFqcn(): string
    {
        return CursoAsignatura::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $fields = [];

        $fields[] = AssociationField::new('curso');
        $fields[] = AssociationField::new('asignatura');

        if($pageName == 'index'){
            $fields[] = AssociationField::new('profesor');
            $fields[] = CollectionField::new('notas', 'Notas');
        }

        if($pageName == 'edit' || $pageName == 'new'){
            $fields[] = AssociationField::new('profesor')->setQueryBuilder(
                fn (QueryBuilder $queryBuilder) => $queryBuilder->getEntityManager()->getRepository(User::class)->findByRol('ROLE_PROFESOR')
            );
            // $fields [] = AssociationField::new('alumnos');
            $fields [] = CollectionField::new('notas', 'Notas')
            ->setEntryType(\App\Form\NotaType::class);            
        }

        return $fields;
    }
}
