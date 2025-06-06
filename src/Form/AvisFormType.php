<?php

namespace App\Form;

use App\Entity\Avis;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AvisFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('note', IntegerType::class, [
                'label' => 'Note (1 à 5 étoiles)',
                'attr' => [
                    'min' => 1,
                    'max' => 5,
                ]
            ])
            // Commentaire facultatif
            ->add('commentaire', TextareaType::class, [
                'label' => 'Votre commentaire',
                'required' => false,
                'attr' => [
                    'rows' => 4,
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Avis::class,
        ]);
    }
}
