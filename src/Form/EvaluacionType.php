<?php

namespace App\Form;

use App\Entity\Evaluacion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class EvaluacionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('exposicionestimada', TextareaType::class, [
                'required' => true
            ])
            ->add('accionesdeseguridad', TextareaType::class, [
                'required' => true
            ])
            ->add('cambiosdeinformacion', TextareaType::class, [
                'required' => true
            ])
            ->add('informaciondeestudios', TextareaType::class, [
                'required' => true
            ])
            ->add('casosindividuales', TextareaType::class, [
                'required' => true
            ])
            ->add('clasificacionriesgosimportantes', TextareaType::class, [
                'required' => true
            ])
            ->add('clasificacionriesgospotenciales', TextareaType::class, [
                'required' => true
            ])
            ->add('clasificacionriesgosinformacionfaltante', TextareaType::class, [
                'required' => true
            ])
            ->add('caracterizacionderiesgos', TextareaType::class, [
                'required' => true
            ])
            ->add('sennalesdeseguridad', TextareaType::class, [
                'required' => true
            ])
            ->add('evaluacionbeneficioriesgo', TextareaType::class, [
                'required' => true
            ])
            ->add('gestionderiesgo', TextareaType::class, [
                'required' => true
            ])
            ->add('conclusionesdeltitular', TextareaType::class, [
                'required' => true
            ])

            ->add('fecha', DateTimeType::class, [
            'format' => 'dd-MM-yyyy',
            'widget' => 'single_text',
            'html5' => false
            ])

            ->add('resultado')
            ->add('recomendacion')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Evaluacion::class,
        ]);
    }
}
