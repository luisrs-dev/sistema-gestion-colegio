<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCrudController extends AbstractCrudController
{

    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
    return $crud
    ->setPageTitle('new', 'Nuevo usuario')
    ->setPageTitle('index', 'Lista de usuarios')
    ->setDefaultSort(['id' => 'DESC'])
    ->setPaginatorPageSize(40)
    ;
    }

    public function configureFields(string $pageName): iterable
    {

        $fields = [];
        $fields[] = TextField::new('username', 'Nombre de usuario');
        $fields[] = TextField::new('fullName', 'Nombre');
        // $fields[] = TextField::new('email', 'Email');

        if($pageName == 'index'){
            $fields[] = ChoiceField::new('rol', 'Rol')
            ->setChoices(fn() => [
                'Administrador' => 'ROLE_ADMIN',
                'Profesor' => 'ROLE_PROFESOR',
                'Alumno' => 'ROLE_ALUMNO'
            ]); 
        }
        
        if($pageName == 'new' || $pageName == 'edit'){
            $fields[] = TextField::new('plainPassword', 'Contraseña')->setFormType(PasswordType::class)->setRequired(false);
            $fields[] = ChoiceField::new('rol', 'Roles')
                ->setChoices(fn() => [
                    'Administrador' => 'ROLE_ADMIN',
                    'Profesor' => 'ROLE_PROFESOR',
                    'Alumno' => 'ROLE_ALUMNO'
                ]);

        }

        return $fields;
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {   
        if ($entityInstance->getPassword() !== '' && $entityInstance->getPassword() === null) {
            $hashedPassword = $this->passwordHasher->hashPassword(
                $entityInstance,
                $entityInstance->plainPassword
            );
            $entityInstance->setPassword($hashedPassword);
        }

        $entityManager->persist($entityInstance);
        $entityManager->flush();
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $hashedPassword = $this->passwordHasher->hashPassword(
            $entityInstance,
            $entityInstance->plainPassword
        );
        $entityInstance->setPassword($hashedPassword);


        $entityManager->persist($entityInstance);
        $entityManager->flush();
    }

}
