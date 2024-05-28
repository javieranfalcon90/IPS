<?php

namespace App\Form;

use App\Entity\Asignacion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class AsignacionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('tramite')
            ->add('fecha', DateTimeType::class, [
                'format' => 'dd-MM-yyyy',
                'widget' => 'single_text',
                'html5' => false
            ])
            ->add('producto')
            ->add('evaluador')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Asignacion::class,
        ]);
    }
}
