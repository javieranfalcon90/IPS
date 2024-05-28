<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Evaluador;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('email')
            ->add('roles',ChoiceType::class, array(
                'required' => true,
                'multiple' => true,
                'choices' => array(
                    'ROLE_ADMINISTRADOR' => 'ROLE_ADMINISTRADOR',
                    'ROLE_SUPERVISOR' => 'ROLE_SUPERVISOR',
                    'ROLE_EVALUADOR' => 'ROLE_EVALUADOR',
                    'ROLE_CONSULTOR' => 'ROLE_CONSULTOR',
                ))
            )
            ->add('password', RepeatedType::class, array(
                'required' => false,
                'type' => PasswordType::class,
                'first_options' => array('label' => 'password'),
                'second_options' => array('label' => 'repetir_password'),
            ))
            ->add('evaluador', EntityType::class, array(
                'placeholder' => true,
                'class' => Evaluador::class,
                'required' => false
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
