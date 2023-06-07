<?php

namespace App\Form;

use App\Entity\Alumno;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AlumnoOldType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', null, array(
                'label' => 'RUT'
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
            // ->add('fechaCreacion')
            // ->add('fechaActualizacion')
            // ->add('cursoAsignatura')
            ->add('curso')
            // ->add('Registrar', SubmitType::class)

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Alumno::class,
        ]);
    }
}
