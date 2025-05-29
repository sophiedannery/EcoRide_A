<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserStatutType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('statut', ChoiceType::class, [
                'choices' => [
                    'Passager' => 'passager',
                    'Chauffeur' => 'chauffeur',
                    'Les deux' => 'passager_chauffeur',
                ],
                'expanded' => true,
                'multiple' => false,
                'label' => 'Je suis :',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
