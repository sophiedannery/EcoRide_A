<?php

namespace App\Form;

use App\Entity\Preference;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserPreferenceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('smokingPreference', EntityType::class, [
                'class' => Preference::class,
                'choice_label' => 'libelle',
                'query_builder' => function ($repo) {
                    return $repo->createQueryBuilder('p')
                        ->where('p.libelle IN (:vals)')
                        ->setParameter('vals', ['Fumeur', 'Non-fumeur']);
                },
                'multiple' => false,
                'expanded' => true,
                'mapped' => false,
                'label' => 'Fumeur ?',
                'required' => true,
            ])
            ->add('animalPreference', EntityType::class, [
                'class' => Preference::class,
                'choice_label' => 'libelle',
                'query_builder' => function ($repo) {
                    return $repo->createQueryBuilder('p')
                        ->where('p.libelle IN (:vals)')
                        ->setParameter('vals', ['Avec animaux', 'Sans animaux']);
                },
                'multiple' => false,
                'expanded' => true,
                'mapped' => false,
                'label' => 'Animaux ?',
                'required' => true,

            ])
            ->add('otherPreferences', ChoiceType::class, [
                'choices' => [
                    'Musique' => 'Musique',
                    'Silence' => 'Silence',
                    'Détour' => 'Détour',
                    'Sport' => 'Sport',
                ],
                'label' => 'Autres préférences',
                'expanded' => true,
                'multiple' => true,
                'mapped' => false,
                'required' => false,
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
