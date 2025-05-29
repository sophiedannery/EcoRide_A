<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RidesController extends AbstractController
{
    #[Route('/rides', name: 'app_rides')]
    public function index(): Response
    {
        return $this->render('rides/index.html.twig');
    }
}
