<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LegalController extends AbstractController
{
    #[Route('/legal', name: 'app_legal')]
    public function index(): Response
    {
        return $this->render('legal/index.html.twig', []);
    }

    #[Route('/cgu', name: 'app_cgu')]
    public function cgu(): Response
    {
        return $this->render('legal/cgu.html.twig', []);
    }

    #[Route('/politique-confidentialite', name: 'app_politique_conf')]
    public function politiqueConfidentialite(): Response
    {
        return $this->render('legal/politique-confidentialite.html.twig', []);
    }
}
