<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Entity\User;
use App\Repository\ReservationRepository;
use App\Repository\TrajetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class EmployeeController extends AbstractController
{
    #[Route('/employee', name: 'app_employee')]
    #[IsGranted('ROLE_EMPLOYEE')]
    public function index(): Response
    {
        return $this->render('employee/index.html.twig', [
            'controller_name' => 'EmployeeController',
        ]);
    }


    #[Route('/employee/signalement', name: 'app_employee_signalement')]
    #[IsGranted('ROLE_EMPLOYEE')]
    public function listeSignalements(ReservationRepository $reservationRepo): Response
    {

        $signalements = $reservationRepo->findSignaledReservations();



        return $this->render('employee/signalement.html.twig', [
            'signalements' => $signalements,
        ]);
    }


    #[Route('/employee/reservation/{id}/cloturer', name: 'app_employee_cloturer_reservation', methods: ['POST'])]
    #[IsGranted('ROLE_EMPLOYEE')]
    public function cloturerReservation(int $id, Request $request, ReservationRepository $reservationRepo, TrajetRepository $trajetRepo, EntityManagerInterface $em): Response
    {

        if (!$this->isCsrfTokenValid('cloturer_reservation' . $id, $request->request->get('_token'))) {
            $this->addFlash('error', 'Token invalide.');
            return $this->redirectToRoute('app_employee_signalement');
        }

        /** @var Reservation|null $reservation */
        $reservation = $reservationRepo->find($id);
        if (!$reservation) {
            $this->addFlash('error', 'Réservation introuvable');
            return $this->redirectToRoute('app_employee_signalement');
        }

        $trajet = $reservation->getTrajet();
        if (!$trajet) {
            $this->addFlash('error', 'Trajet introuvable');
            return $this->redirectToRoute('app_employee_signalement');
        }

        $reservation->setStatut('validé');

        $prixPayé = $reservation->getCreditsUtilises();
        $commission = 2;
        $netChauffeur = max(0, $prixPayé - $commission);

        $chauffeur = $trajet->getChauffeur();
        $chauffeur->setCredits($chauffeur->getCredits() + $netChauffeur);

        $txChauffeur = new Transaction();
        $txChauffeur
            ->setUser($chauffeur)
            ->setTrajet($trajet)
            ->setMontant($netChauffeur)
            ->setType('paiement_chauffeur');
        $em->persist($txChauffeur);

        $plateforme = $em->getRepository(User::class)->find(2);

        $txCommission = new Transaction();
        $txCommission
            ->setUser($plateforme)
            ->setTrajet($trajet)
            ->setMontant($commission)
            ->setType('commission_plateforme');
        $em->persist($txCommission);

        $autresReservations = $reservationRepo->findBy(['trajet' => $trajet]);
        $toutValide = true;
        foreach ($autresReservations as $autre) {
            if ($autre->getStatut() !== 'validé') {
                $toutValide = false;
                break;
            }
        }

        if ($toutValide) {
            $trajet->setStatut('validé');
            $em->persist($trajet);
        }

        $em->persist($reservation);
        $em->persist($trajet);
        $em->flush();

        $this->addFlash('success', 'La réservation a été clôturée et le trajet validé avec succès.');
        return $this->redirectToRoute('app_employee_signalement');
    }
}
