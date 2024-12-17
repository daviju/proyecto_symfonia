<?php

namespace App\Form;

use App\Entity\Pregunta;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PreguntaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titulo', TextType::class, [
                'label' => 'Título',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ingrese el título de la pregunta',
                ],
            ])
            ->add('fecha_inicio', DateTimeType::class, [ // Corregido
                'label' => 'Fecha Inicio',
                'widget' => 'single_text',
                'input' => 'datetime',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('fecha_fin', DateTimeType::class, [ // Corregido
                'label' => 'Fecha Fin',
                'widget' => 'single_text',
                'input' => 'datetime',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('r1', TextType::class, [
                'label' => 'Respuesta 1',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ingrese la primera respuesta',
                ],
            ])
            ->add('r2', TextType::class, [
                'label' => 'Respuesta 2',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ingrese la segunda respuesta',
                ],
            ])
            ->add('r3', TextType::class, [
                'label' => 'Respuesta 3',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ingrese la tercera respuesta (opcional)',
                ],
            ])
            ->add('r4', TextType::class, [
                'label' => 'Respuesta 4',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ingrese la cuarta respuesta (opcional)',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pregunta::class,
        ]);
    }
}
