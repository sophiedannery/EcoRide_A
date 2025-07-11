<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Vehicule;
use Doctrine\ORM\EntityManagerInterface;

class ChauffeurService
{
    public function __construct(
        private EntityManagerInterface $em,
        private MongoService $mongo
    ) {}

    public function registerChauffeur(User $user, array $vehiculeData, array $preferenceData): void
    {
        $vehicule = new Vehicule;
        $vehicule->setUser($user)
            ->setMarque($vehiculeData['marque'])
            ->setModele($vehiculeData['modele'])
            ->setCouleur($vehiculeData['couleur'])
            ->setImmatriculation($vehiculeData['immatriculation'])
            ->setPlacesDisponibles($vehiculeData['places'])
            ->setEnergie($vehiculeData['energie'])
            ->setDatePremiereImmatriculation(new \DateTime($vehiculeData['date']));

        $this->em->persist($vehicule);

        $this->mongo->savePreferences($user->getId(), $preferenceData);

        $user->setStatut('chauffeur');

        $this->em->flush();
    }
}
