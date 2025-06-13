<?php

namespace App\Form;

use App\Entity\Trajet;
use App\Entity\Vehicule;
use App\Repository\VehiculeRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;

class TrajetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        /** @var \App\Entity\User $user */
        $user = $options['user'];

        $builder
            ->add('adresseDepart', TextType::class, [
                'label' => 'Adresse de départ',
            ])
            ->add('adresseArrivee', TextType::class, [
                'label' => 'Adresse d\'arrivée',
            ])
            ->add('dateDepart', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date et heure de départ',
            ])
            ->add('dateArrivee', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date et heure d\'arrivée',
            ])
            ->add('prix', IntegerType::class, [
                'label'       => 'Prix ',
                'attr'        => [
                    'min'  => 2,
                    'step' => 1,
                ],
                'constraints' => [
                    new GreaterThan([
                        'value'   => 2,
                        'message' => 'Le prix doit être supérieur à 2 crédits (commission plateforme).',
                    ]),
                ],
                'help' => '2 crédits de commission plateforme seront automatiquement retirés.',
            ])
            ->add('vehicule', EntityType::class, [
                'class'         => Vehicule::class,
                'choice_label'  => 'immatriculation',
                'query_builder' => function (VehiculeRepository $repo) use ($user) {
                    return $repo->createQueryBuilder('v')
                        ->where('v.user = :u')
                        ->setParameter('u', $user);
                },
                'required'     => true,
                'mapped'       => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trajet::class,
            // on ajoute la définition de l'option
            'user'       => null,
        ]);
        $resolver->setAllowedTypes('user', ['null', \App\Entity\User::class]);
    }
}
