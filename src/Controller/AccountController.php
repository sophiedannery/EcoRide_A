<?php

namespace App\Controller;

use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class AccountController extends AbstractController
{
    #[Route('/account', name: 'app_account')]
    #[IsGranted('ROLE_USER')]
    public function index(ReservationRepository $reservation_repository): Response
    {

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $history = $reservation_repository->findHistoryByUser($user->getId());


        return $this->render('account/index.html.twig', [
            'history' => $history,
        ]);
    }
}
