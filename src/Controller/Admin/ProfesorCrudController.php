<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
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

class ProfesorCrudController extends AbstractCrudController
{

    private AdminUrlGenerator $adminUrlGenerator;
    private ?CsrfTokenManagerInterface $csrfTokenManager;
    private $entityManager;
    private $entityRepository;
    private UserPasswordHasherInterface $passwordHasher;


    public function __construct(EntityManagerInterface $entityManager, AdminUrlGenerator $adminUrlGenerator, ?CsrfTokenManagerInterface $csrfTokenManager = null, EntityRepository $entityRepository, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->adminUrlGenerator = $adminUrlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
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
        ->setParameter('rol', 'ROLE_PROFESOR');
        return $response;
    }


    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance) : void
    {  

        $entityInstance->setRol('ROLE_PROFESOR');

        $hashedPassword = $this->passwordHasher->hashPassword(
            $entityInstance,
            $entityInstance->plainPassword
        );
        $entityInstance->setPassword($hashedPassword);

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
        $fields[] = EmailField::new('email', 'Correo electrónico');
        $fields[] = TextField::new('fullname', 'Nombre completo');
        $fields[] = TextField::new('phone');
        // $fields[] = AssociationField::new('curso');

        // if($pageName == 'index'){

        // }


        return $fields;
    }
}
