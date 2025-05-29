<?php

namespace App\Controller;

use App\Entity\Preference;
use App\Form\UserPreferenceType;
use App\Form\UserStatutType;
use App\Repository\PreferenceRepository;
use App\Repository\ReservationRepository;
use App\Repository\TrajetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class AccountController extends AbstractController
{
    #[Route('/account', name: 'app_account')]
    #[IsGranted('ROLE_USER')]
    public function index(Request $request, EntityManagerInterface $em, ReservationRepository $reservation_repository, TrajetRepository $trajet_repository): Response
    {

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $statutForm = $this->createForm(UserStatutType::class, $user);
        $statutForm->handleRequest($request);

        if ($statutForm->isSubmitted() && $statutForm->isValid()) {
            $em->flush();

            if (in_array($user->getStatut(), ['chauffeur', 'passager_chauffeur'], true)) {
                $needsVehicule = $user->getVehicules()->isEmpty();
                $needsPreferences = $user->getPreferences()->isEmpty();
                if ($needsVehicule || $needsPreferences) {
                    $this->addFlash('warning', 'En tant que chauffeur, vous devez ajouter une voiture'
                        . ($needsVehicule && $needsPreferences ? ' et des préférences.' : ($needsVehicule ? ' puis ajouter une voiture.' : ' puis définir vos préférences.')));
                    return $this->redirectToRoute($needsVehicule ? 'app_account_vehicule_new' : 'app_account_preferences');
                }
            }

            $this->addFlash('success', 'Statut mis à jour');
            return $this->redirectToRoute('app_account');
        }


        $vehicules = $user->getVehicules();
        $history = $reservation_repository->findHistoryByUser($user->getId());
        $driverTrips = $trajet_repository->findTripsByDriver($user->getId());


        return $this->render('account/index.html.twig', [
            'statutForm' => $statutForm,
            'history' => $history,
            'driverTrips' => $driverTrips,
            'vehicules' => $vehicules,
        ]);
    }


    #[Route('/account/preferences', name: 'app_account_preferences', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function preferences(Request $request, EntityManagerInterface $em, PreferenceRepository $prefRepo): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $form = $this->createForm(UserPreferenceType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var User $user */
            $user = $this->getUser();

            $smokingPref = $form->get('smokingPreference')->getData();
            $animalPref = $form->get('animalPreference')->getData();

            foreach ($user->getPreferences() as $pref) {
                if (in_array($pref->getLibelle(), ['Fumeur', 'Non-fumeur', 'Avec animaux', 'Sans animaux'])) {
                    $user->removePreference($pref);
                }
            }

            $user->addPreference($smokingPref);
            $user->addPreference($animalPref);

            $others = $form->get('otherPreferences')->getData();
            if ($others) {
                foreach ($others as $label) {
                    $pref = $prefRepo->findOneBy(['libelle' => $label]);
                    if (!$pref) {
                        $pref = new Preference();
                        $pref->setLibelle($label);
                        $em->persist($pref);
                    }

                    $user->addPreference($pref);
                }
            }



            $em->flush();
            $this->addFlash('success', 'Préférences enregistrées.');
            return $this->redirectToRoute('app_account');
        }

        return $this->render('account/preferences.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
