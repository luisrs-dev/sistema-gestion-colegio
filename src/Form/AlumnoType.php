<?php

namespace App\Form;

use App\Entity\Alumno;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AlumnoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

        ->add('username', null, array(
            'label' => 'RUT'
        ))
        ->add('password', null, array(
            'label' => 'Password'
        ))

        ->add('fullname', null, array(
            'label' => 'Nombre completo'
        ))
        ->add('phone', null, array(
            'label' => 'Teléfono'
        ))
        ->add('email', null, array(
            'label' => 'Email'
        ))
            // ->add('roles', CollectionType::class, [
            //     'entry_type'   => ChoiceType::class,
            //     'entry_options'  => [
            //         'choices'  => [
            //             'Alumno' => 'Alumno',
            //             'Profesor' => 'Profesor',
            //             'Administrador' => 'Administrador'
            //             ],
            //     ],
            // ])

            // ->add('fechaCreacion')
            // ->add('fechaActualizacion')
            // ->add('cursoAsignatura')
            ->add('curso')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Alumno::class,
        ]);
    }
}
