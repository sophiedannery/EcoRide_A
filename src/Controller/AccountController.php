<?php

namespace App\Controller;

use App\Entity\Preference;
use App\Form\ProfileFormType;
use App\Form\UserPreferenceType;
use App\Form\UserStatutType;
use App\Repository\AvisRepository;
use App\Repository\PreferenceRepository;
use App\Repository\ReservationRepository;
use App\Repository\TrajetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

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

        foreach ($driverTrips as &$trip) {
            $tripId = $trip['id_trajet'];
            $passagers = $reservation_repository->findPassengerPseudoByTrajet($tripId);
            $trip['passagers'] = $passagers;
        }

        return $this->render('account/index.html.twig', [
            'statutForm' => $statutForm,
            'history' => $history,
            'driverTrips' => $driverTrips,
            'vehicules' => $vehicules,
        ]);
    }


    #[Route('/account/statut', name: 'app_account_statut')]
    #[IsGranted('ROLE_USER')]
    public function statut(Request $request, EntityManagerInterface $em, ReservationRepository $reservation_repository, TrajetRepository $trajet_repository): Response
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

        return $this->render('account/statut.html.twig', [
            'statutForm' => $statutForm,
            'vehicules' => $vehicules,
        ]);
    }


    #[Route('/account/reservations', name: 'app_account_reservations')]
    #[IsGranted('ROLE_USER')]
    public function reservations(Request $request, EntityManagerInterface $em, ReservationRepository $reservation_repository, TrajetRepository $trajet_repository): Response
    {

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $vehicules = $user->getVehicules();
        $history = $reservation_repository->findHistoryByUser($user->getId());
        $driverTrips = $trajet_repository->findTripsByDriver($user->getId());

        foreach ($driverTrips as &$trip) {
            $tripId = $trip['id_trajet'];
            $passagers = $reservation_repository->findPassengerPseudoByTrajet($tripId);
            $trip['passagers'] = $passagers;
        }

        return $this->render('account/reservations.html.twig', [
            'history' => $history,
            'driverTrips' => $driverTrips,
            'vehicules' => $vehicules,
        ]);
    }



    #[Route('/account/trajets', name: 'app_account_trajets')]
    #[IsGranted('ROLE_USER')]
    public function trajets(Request $request, EntityManagerInterface $em, ReservationRepository $reservation_repository, TrajetRepository $trajet_repository): Response
    {

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $vehicules = $user->getVehicules();
        $history = $reservation_repository->findHistoryByUser($user->getId());
        $driverTrips = $trajet_repository->findTripsByDriver($user->getId());

        foreach ($driverTrips as &$trip) {
            $tripId = $trip['id_trajet'];
            $passagers = $reservation_repository->findPassengerPseudoByTrajet($tripId);
            $trip['passagers'] = $passagers;
        }



        return $this->render('account/trajets.html.twig', [
            'history' => $history,
            'driverTrips' => $driverTrips,
            'vehicules' => $vehicules,
        ]);
    }

    #[Route('/account/vehicules', name: 'app_account_vehicules')]
    #[IsGranted('ROLE_USER')]
    public function vehicules(Request $request, EntityManagerInterface $em, ReservationRepository $reservation_repository, TrajetRepository $trajet_repository): Response
    {

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $vehicules = $user->getVehicules();

        return $this->render('account/vehicules.html.twig', [
            'vehicules' => $vehicules,
        ]);
    }


    #[Route('/account/avis', name: 'app_account_avis')]
    #[IsGranted('ROLE_USER')]
    public function mesAvis(AvisRepository $avisRepo): Response
    {

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $avisRecus = $avisRepo->findAvisByChauffeur($user->getId());

        return $this->render('account/avis.html.twig', [
            'avisRecus' => $avisRecus,
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
            return $this->redirectToRoute('app_account_preferences');
        }

        return $this->render('account/preferences.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/account/edit', name: 'app_account_edit')]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(ProfileFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $photoFile */
            $photoFile = $form->get('photoFile')->getData();

            if ($photoFile) {
                $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $photoFile->guessExtension();

                try {
                    $photoFile->move(
                        $this->getParameter('user_photos_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors de l\'enregistrement de la photo.');
                    return $this->redirectToRoute('app_account_edit');
                }

                $oldFilename = $user->getPhotoFilename();
                if ($oldFilename) {
                    $oldFilepath = $this->getParameter('user_photos_directory') . '/' . $oldFilename;
                    if (file_exists($oldFilepath)) {
                        @unlink($oldFilepath);
                    }
                }
                $user->setPhotoFilename($newFilename);
            }

            $em->flush();

            $this->addFlash('success', 'Votre photo de profil a bien été mise à jour.');
            return $this->redirectToRoute('app_account');
        }


        return $this->render('account/edit.html.twig', [
            'profileForm' => $form->createView(),
        ]);
    }

    #[Route('/account/show', name: 'app_account_show')]
    #[IsGranted('ROLE_USER')]
    public function show(): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('account/show.html.twig', [
            'user' => $user,
        ]);
    }
}
