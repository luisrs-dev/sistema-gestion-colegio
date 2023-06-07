<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class AlumnoCrudController extends AbstractCrudController
{

    private $entityRepository;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityRepository $entityRepository, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityRepository = $entityRepository;
        $this->passwordHasher = $passwordHasher;


    }


    public static function getEntityFqcn(): string
    {
        return User::class;
    }


    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $response = $this->entityRepository->createQueryBuilder($searchDto, $entityDto, $fields, $filters);

        $response->andwhere('entity.rol = :rol')
        ->setParameter('rol', 'ROLE_ALUMNO');
        return $response;
    }

    public function configureCrud(Crud $crud): Crud
    {
    return $crud
        ->setPageTitle('index', 'Alumnos')
    ;
    }

    public function configureActions(Actions $actions): Actions
    {
        $registroMasivo = Action::new('export_xlsx', 'Carga masiva', 'fas fa-upload')
        ->createAsGlobalAction()
        ->setCssClass('btn btn-primary')
        ->linkToRoute('carga_masiva_alumnos');

        return $actions
        ->add(Crud::PAGE_INDEX, $registroMasivo);
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance) : void
    {  

        $hashedPassword = $this->passwordHasher->hashPassword(
            $entityInstance,
            $entityInstance->plainPassword
        );
        $entityInstance->setPassword($hashedPassword);

        $entityInstance->setRol('ROLE_ALUMNO');
        $entityManager->persist($entityInstance);
        $entityManager->flush();

    }


    public function configureFields(string $pageName): iterable
    {

        $fields = [];
        $fields[] = TextField::new('username', 'RUT');
        if($pageName == 'new' | $pageName == 'edit'){
            $fields[] = TextField::new('password', 'Contraseña')->setFormType(PasswordType::class)->setRequired(false);
        }
        // $fields[] = EmailField::new('email', 'Correo electrónico');
        $fields[] = TextField::new('fullname', 'Nombre completo');
        $fields[] = TextField::new('phone');
        $fields[] = AssociationField::new('curso', 'Curso');

        // $fields[] = AssociationField::new('curso');

        // if($pageName == 'index'){

        // }


        return $fields;
    }
}
