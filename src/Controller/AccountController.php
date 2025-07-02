<?php

namespace App\Controller;

use App\Entity\Preference;
use App\Form\ProfileFormType;
use App\Form\UserPreferenceType;
use App\Form\UserStatutType;
use App\Repository\PreferenceRepository;
use App\Repository\ReservationRepository;
use App\Repository\TrajetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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

        return $this->render('account/index.html.twig', [
            'user' => $user,
        ]);
    }


    #[Route('/account/infos', name: 'app_account_infos')]
    #[IsGranted('ROLE_USER')]
    public function infos(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(ProfileFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $photoFile = $form->get('photoFile')->getData();

            if ($photoFile instanceof UploadedFile && !$photoFile->isValid()) {
                $errors = [
                    UPLOAD_ERR_INI_SIZE   => 'Le fichier est trop volumineux.',
                    UPLOAD_ERR_FORM_SIZE  => 'Le fichier est trop volumineux.',
                    UPLOAD_ERR_PARTIAL    => 'Le fichier a été partiellement téléchargé.',
                    UPLOAD_ERR_NO_FILE    => 'Aucun fichier n’a été téléchargé.',
                    UPLOAD_ERR_NO_TMP_DIR => 'Pas de dossier temporaire configuré.',
                    UPLOAD_ERR_CANT_WRITE => 'Erreur d’écriture du fichier sur le serveur.',
                    UPLOAD_ERR_EXTENSION  => 'Téléchargement interrompu.',
                ];
                $message = $errors[$photoFile->getError()] ?? 'Erreur lors du téléchargement.';
                $this->addFlash('warning', $message);

                return $this->redirectToRoute('app_account_infos');
            }

            if ($form->isValid()) {
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
                        $this->addFlash('warning', 'Une erreur est survenue lors de l\'enregistrement de la photo.');
                        return $this->redirectToRoute('app_account_infos');
                    }

                    // Suppression ancienne photo
                    if ($oldFilename = $user->getPhotoFilename()) {
                        $oldFilepath = $this->getParameter('user_photos_directory') . '/' . $oldFilename;
                        if (file_exists($oldFilepath)) {
                            @unlink($oldFilepath);
                        }
                    }

                    $user->setPhotoFilename($newFilename);
                }

                $em->flush();
                $this->addFlash('success', 'Votre photo de profil a bien été mise à jour.');
                return $this->redirectToRoute('app_account_infos');
            }
        }

        return $this->render('account/infos.html.twig', [
            'profileForm' => $form->createView(),
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
}
