<?php

namespace App\Controller;

use App\Repository\TrajetRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(UserRepository $userRepository): Response
    {


        return $this->render('admin/index.html.twig', []);
    }


    #[Route('/admin/dashboard', name: 'app_admin_dashboard')]
    #[IsGranted('ROLE_ADMIN')]
    public function dashboard(TrajetRepository $trajet_repository): Response
    {

        $countsByDate = $trajet_repository->findCountByDate();

        $labels = array_keys($countsByDate);
        $data = array_values($countsByDate);


        return $this->render('admin/dashboard.html.twig', [
            'labels' => $labels,
            'data' => $data,
        ]);
    }
}
