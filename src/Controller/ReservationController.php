<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\Transaction;
use App\Repository\ReservationRepository;
use App\Repository\TrajetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ReservationController extends AbstractController
{
    #[Route('/trajet/{id}/participer', name: 'app_trajet_participer', methods: ['POST'])]
    public function participer(int $id, Request $request, TrajetRepository $trajetRepo, EntityManagerInterface $em): Response
    {

        $this->denyAccessUnlessGranted('ROLE_USER');



        if (!$this->isCsrfTokenValid('participate' . $id, $request->request->get('_token'))) {
            $this->addFlash('error', 'Requête invalide, token CSRF non validé.');
            return $this->redirectToRoute('app_trajet_detail', ['id' => $id]);
        }



        $trajet = $trajetRepo->find($id);

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if (!$trajet) {
            $this->addFlash('error', 'Trajet introuvable.');
            return $this->redirectToRoute('app_search');
        }

        if ($trajet->getChauffeur() === $user) {
            $this->addFlash('error', 'Vous ne pouvez pas participer à votre propre trajet.');
            return $this->redirectToRoute('app_trajet_detail', ['id' => $id]);
        }



        $exists = $em->getRepository(Reservation::class)->findOneBy(['trajet' => $trajet, 'passager' => $user]);

        if ($exists) {
            $this->addFlash('warning', 'Vous participez déjà à ce trajet.');
            return $this->redirectToRoute('app_trajet_detail', ['id' => $id]);
        }

        if ($trajet->getPlacesRestantes() < 1) {
            $this->addFlash('error', 'Plus de place disponible sur ce trajet.');
            return $this->redirectToRoute('app_trajet_detail', ['id' => $id]);
        }



        $prix = $trajet->getPrix();

        if ($user->getCredits() < $prix) {
            $this->addFlash('error', 'Crédits insuffisants pour réserver ce trajet.');
            return $this->redirectToRoute('app_trajet_detail', ['id' => $id]);
        }

        $reservation = new Reservation();
        $reservation
            ->setTrajet($trajet)
            ->setPassager($user)
            ->setDateConfirmation(new \DateTime())
            ->setStatut('confirmée')
            ->setCreditsUtilises($prix);
        $em->persist($reservation);

        $trajet->setPlacesRestantes($trajet->getPlacesRestantes() - 1);
        $user->setCredits($user->getCredits() - $prix);



        $transaction = new Transaction();
        $transaction
            ->setUser($user)
            ->setTrajet($trajet)
            ->setMontant($prix)
            ->setType('paiement');
        $em->persist($transaction);

        $em->flush();

        $this->addFlash('success', 'Votre réservation est confirmée !');


        return $this->redirectToRoute('app_account');
    }

    #[Route('/reservation/{id}/annuler', name: 'app_reservation_annuler', methods: ['POST'])]
    public function annulerReservation(int $id, Request $request, ReservationRepository $reservationRepo, TrajetRepository $trajetRepo, EntityManagerInterface $em): Response
    {
        if (!$this->isCsrfTokenValid('cancel_reservation' . $id, $request->request->get('_token'))) {
            $this->addFlash('error', 'Token invalide.');
            return $this->redirectToRoute('app_account');
        }

        /** @var Reservation|null $reservation */
        $reservation = $reservationRepo->find($id);

        if (!$reservation) {
            $this->addFlash('error', 'Réservation introuvable.');
            return $this->redirectToRoute('app_account');
        }

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if ($reservation->getPassager()->getId() !== $user->getId()) {
            $this->addFlash('error', 'Vous ne pouvez pas annuler cette réservation.');
            return $this->redirectToRoute('app_account');
        }

        $trajet = $reservation->getTrajet();

        $creditsUtilises = $reservation->getCreditsUtilises();
        $user->setCredits($user->getCredits() + $creditsUtilises);

        $trajet->setPlacesRestantes($trajet->getPlacesRestantes() + 1);

        $transaction = new Transaction();
        $transaction
            ->setUser($user)
            ->setTrajet($trajet)
            ->setMontant($creditsUtilises)
            ->setType('remboursement_passager_annulation');
        $em->persist($transaction);

        $em->remove($reservation);

        $em->flush();
        $this->addFlash('success', 'Votre réservation a bien été annulé et vos crédits remboursés.');

        return $this->redirectToRoute('app_account');
    }
}
