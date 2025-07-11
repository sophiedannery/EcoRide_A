<?php

namespace App\Controller;

use App\Entity\Vehicule;
use App\Form\VehiculeForm;
use App\Service\ChauffeurService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ChauffeurController extends AbstractController
{



    #[Route('/chauffeur/new/step-1', name: 'app_become_chauffeur_vehicule')]
    #[IsGranted('ROLE_USER')]
    public function becomeChauffeurVehicule(Request $request): Response
    {
        $session = $request->getSession();
        $vehiculeData = $session->get('vehicule_data', []);

        $vehicule = new Vehicule();

        if ($vehiculeData) {
            $vehicule->setMarque($vehiculeData['marque'] ?? null);
            $vehicule->setModele($vehiculeData['modele'] ?? null);
            $vehicule->setCouleur($vehiculeData['couleur'] ?? null);
            $vehicule->setImmatriculation($vehiculeData['immatriculation'] ?? null);
            $vehicule->setPlacesDisponibles($vehiculeData['places'] ?? null);
            $vehicule->setEnergie($vehiculeData['energie'] ?? null);
            if (!empty($vehiculeData['date'])) {
                $vehicule->setDatePremiereImmatriculation(new \DateTime($vehiculeData['date']));
            }
        }

        $form = $this->createForm(VehiculeForm::class, $vehicule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $session = $request->getSession();
            $session->set('vehicule_data', [
                'marque' => $vehicule->getMarque(),
                'modele' => $vehicule->getModele(),
                'couleur' => $vehicule->getCouleur(),
                'immatriculation' => $vehicule->getImmatriculation(),
                'places' => $vehicule->getPlacesDisponibles(),
                'energie' => $vehicule->getEnergie(),
                'date' => $vehicule->getDatePremiereImmatriculation()?->format('Y-m-d'),
            ]);
            return $this->redirectToRoute('app_become_chauffeur_preferences');
        }

        return $this->render('chauffeur/vehicule.html.twig', [
            'form' => $form->createView(),
        ]);
    }



    #[Route('/chauffeur/new/step-2', name: 'app_become_chauffeur_preferences')]
    #[IsGranted('ROLE_USER')]
    public function becomeChauffeurPreferences(Request $request): Response
    {

        $session = $request->getSession();
        $preferences = $session->get('preferences_data', []);

        if ($request->isMethod('POST')) {
            $tabac = $request->request->get('tabac');
            $animaux = $request->request->get('animaux');
            $optionnelles = $request->request->all('preferences');

            $allPreferences = array_filter(array_merge([$tabac, $animaux], $optionnelles), fn($p) => !empty($p));

            $session->set('preferences_data', $allPreferences);

            return $this->redirectToRoute('app_become_chauffeur_summary');
        }

        return $this->render('chauffeur/preferences.html.twig', [
            'preferences' => $preferences,
        ]);
    }


    #[Route('/chauffeur/new/step-3', name: 'app_become_chauffeur_summary')]
    #[IsGranted('ROLE_USER')]
    public function becomeChauffeurSummary(
        Request $request,
        ChauffeurService $chauffeurNewService
    ): Response {

        $session = $request->getSession();
        $vehiculeData = $session->get('vehicule_data');
        $preferenceData = $session->get('preferences_data');

        if (!$vehiculeData || !$preferenceData) {
            $this->addFlash('warning', 'Il manque des informations.');
            return $this->redirectToRoute('app_become_chauffeur_vehicule');
        }

        if ($request->isMethod('POST')) {

            /** @var \App\Entity\User $user */
            $user = $this->getUser();

            $chauffeurNewService->registerChauffeur($user, $vehiculeData, $preferenceData);

            $session->remove('vehicule_data');
            $session->remove('preferences_data');

            $this->addFlash('success', 'Vous êtes maintenant chauffeur !');
            return $this->redirectToRoute('app_account');
        }

        return $this->render('chauffeur/summary.html.twig', [
            'vehicule' => $vehiculeData,
            'preferences' => $preferenceData,
        ]);
    }
}
