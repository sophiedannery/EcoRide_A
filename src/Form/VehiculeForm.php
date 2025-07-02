<?php

namespace App\Form;

use App\Entity\Vehicule;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class VehiculeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('immatriculation', TextType::class, [
                'label' => 'Plaque d\'immatriculation',
            ])
            ->add('datePremiereImmatriculation', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de la première immatriculation',
                'constraints' => [
                    new Assert\LessThanOrEqual([
                        'value' => 'today',
                        'message' => 'Le véhicule doit déjà être immatriculé.',
                    ]),
                ],
            ])
            ->add('marque', TextType::class)
            ->add('modele', TextType::class)
            ->add('couleur', TextType::class)
            ->add('placesDisponibles', IntegerType::class, [
                'label' => 'Nombre de places',
                'constraints' => [
                    new Assert\GreaterThanOrEqual([
                        'value' => 1,
                    ])
                ]
            ])
            ->add('energie', ChoiceType::class, [
                'choices' => [
                    'Essence' => 'essence',
                    'Diesel' => 'diesel',
                    'Électrique' => 'electrique',
                    'Autre' => 'autre',
                ],
                'label' => 'Type d\'énergie',
                'placeholder' => 'Choisir une énergie'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Vehicule::class,
        ]);
    }
}
