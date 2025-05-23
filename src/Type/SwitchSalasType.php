<?php

namespace App\Form;

use App\Entity\SwitchSalas;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SwitchSalasType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre', TextType::class, [
                'label' => 'Nombre de la sala o ubicación',
            ])
            ->add('ip', TextType::class, [
                'label' => 'Dirección IP del switch',
            ])
            ->add('puertos', TextType::class, [
                'label' => 'Puertos Ethenet asignados',
                'required' => false,
                'attr' => ['placeholder' => 'Ejemplo: 1-4,7,9']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SwitchSalas::class,
        ]);
    }
}