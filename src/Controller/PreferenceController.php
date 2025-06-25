<?php

namespace App\Controller;

use App\Service\MongoService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}
