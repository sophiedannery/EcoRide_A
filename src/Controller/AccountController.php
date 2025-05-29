<?php

namespace App\Controller;

use App\Entity\Preference;
use App\Form\UserPreferenceType;
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
    public function index(ReservationRepository $reservation_repository, TrajetRepository $trajet_repository): Response
    {

        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $vehicules = $user->getVehicules();

        $history = $reservation_repository->findHistoryByUser($user->getId());

        $driverTrips = $trajet_repository->findTripsByDriver($user->getId());


        return $this->render('account/index.html.twig', [
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
