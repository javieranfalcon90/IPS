<?php

namespace App\Form;

use App\Entity\Asignacion;
use App\Entity\Solicitud;
use App\Entity\Seccionfaltante;
use App\Entity\Viaadministracion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;


class SolicitudType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('noregistro')

            ->add('fecha', DateTimeType::class, [
                'format' => 'dd-MM-yyyy',
                'widget' => 'single_text',
                'html5' => false
            ])

            ->add('fortaleza')
            ->add('formafarmaceutica')

            ->add('viaadministracion', EntityType::class,[
                'class' => Viaadministracion::class,
                'multiple' => true
            ])
            ->add('solicitante')
            ->add('titular')

            ->add('nacionalidadtitular', ChoiceType::class, [
                'choices'  => [
                    '' => '',
                    'Nacional' => 'Nacional',
                    'Extranjero' => 'Extranjero',
                ],
            ])

            ->add('noips')
            ->add('ipsanterior', DateTimeType::class, [
                'format' => 'dd-MM-yyyy',
                'widget' => 'single_text',
                'html5' => false,
                'required' => false
            ])
            ->add('periodoevaluado')
            ->add('periodoevaluadoadecuado', ChoiceType::class, [
                'choices'  => [
                    '' => '',
                    'Si' => 'Si',
                    'No' => 'No',
                ],
            ])

            ->add('motivopresentacion')
            ->add('regulacionpermitida', ChoiceType::class, [
                'choices'  => [
                    '' => '',
                    'Si' => 'Si',
                    'No' => 'No',
                ],
            ])
            ->add('seccionfaltante', EntityType::class,[
                'class' => Seccionfaltante::class,
                'required' => false,
                'multiple' => true
            ])

            ->add('fechainscripcionmed', DateTimeType::class, [
                'format' => 'dd-MM-yyyy',
                'widget' => 'single_text',
                'html5' => false
            ])
            ->add('fechaultimarenovacion', DateTimeType::class, [
                'format' => 'dd-MM-yyyy',
                'widget' => 'single_text',
                'html5' => false,
                'required' => false
            ])
            ->add('indicacionesaprobadas')

            ->add('fecharcpvigente', DateTimeType::class, [
                'format' => 'dd-MM-yyyy',
                'widget' => 'single_text',
                'html5' => false,
                'required' => false
            ])
            ->add('fechainternacionalmed', DateTimeType::class, [
                'format' => 'dd-MM-yyyy',
                'widget' => 'single_text',
                'html5' => false,
                'required' => false
            ])
            ->add('paises')

            ->add('observaciones');

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Solicitud::class,
        ]);
    }
}
