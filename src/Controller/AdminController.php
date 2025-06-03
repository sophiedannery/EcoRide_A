<?php

namespace App\Controller;

use App\Repository\TrajetRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    public function dashboard(Request $request, TrajetRepository $trajet_repository): Response
    {

        $wkParam = $request->query->get('week_start');
        if ($wkParam) {
            try {
                $startOfWeek = new \DateTimeImmutable($wkParam);
            } catch (\Exception $e) {
                $startOfWeek = new \DateTimeImmutable('monday this week');
            }
        } else {
            $startOfWeek = new \DateTimeImmutable('monday this week');
        }

        if ($startOfWeek->format('N') !== '1') {
            $startOfWeek = $startOfWeek->modify('monday this week');
        }

        $endOfWeek = $startOfWeek->modify('+6 days');




        $countsByDate = $trajet_repository->findCountByDate($startOfWeek, $endOfWeek);

        $labels = [];
        $data = [];
        $cursor = $startOfWeek;
        for ($i = 0; $i < 7; $i++) {
            $jourKey = $cursor->format('Y-m-d');
            $labels[] = $jourKey;
            $data[] = $countsByDate[$jourKey] ?? 0;
            $cursor = $cursor->modify('+1 day');
        }

        $preWeek = $startOfWeek->modify('-7days')->format('Y-m-d');
        $nextWeek = $startOfWeek->modify('+7days')->format('Y-m-d');


        return $this->render('admin/dashboard.html.twig', [
            'labels' => $labels,
            'data' => $data,
            'preWeek' => $preWeek,
            'nextWeek' => $nextWeek,
            'startWeek' => $startOfWeek->format('Y-m-d'),
        ]);
    }
}
