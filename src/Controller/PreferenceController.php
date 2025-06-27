<?php

namespace App\Controller;

use App\Service\MongoService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PreferenceController extends AbstractController
{

    #[Route('/preferences', name: 'preferences')]
    public function preferences(MongoService $mongo): Response
    {
        $mongo->savePreferences(1, ['calme', 'non-fumeur']); // test
        $prefs = $mongo->getPreferences(1);

        return $this->json(['prefs' => $prefs]);
    }


    #[Route('/preferences/edit', name: 'edit_preferences')]
    public function edit(
        Request $request,
        MongoService $mongo
    ): Response {

        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $saved = $mongo->getPreferences($user->getId()) ?? [];

        if ($request->isMethod('POST')) {

            $tabac = $request->request->get('tabac');
            $animaux = $request->request->get('animaux');
            $optionnelles = $request->request->all('preferences');

            $allPreferences = array_filter(array_merge([$tabac, $animaux], $optionnelles), fn($p) => !empty($p));

            $mongo->savePreferences($user->getId(), array_values($allPreferences));

            return $this->redirectToRoute('app_vehicule_account');
        }

        return $this->render('preferences/edit.html.twig', [
            'preferences' => $saved,
        ]);
    }
}
