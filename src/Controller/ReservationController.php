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





    #[Route('/reservation/{id}/resume', name: 'app_reservation_resume', methods: ['GET'])]
    public function resumeReservation(int $id, ReservationRepository $reservationRepo): Response
    {
        /** @var Reservation|null $reservation */
        $reservation = $reservationRepo->find($id);
        if (!$reservation) {
            $this->addFlash('error', 'Réservation introuvable');
            return $this->redirectToRoute('app_account_reservations');
        }

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if ($reservation->getPassager()->getId() !== $user->getId()) {
            $this->addFlash('error', 'Vous ne pouvez pas valider cette réservation.');
            return $this->redirectToRoute('app_account_reservations');
        }

        $trajet = $reservation->getTrajet();
        $chauffeur = $trajet->getChauffeur();
        $adresseDepart = $trajet->getAdresseDepart();
        $adresseArrivee = $trajet->getAdresseArrivee();
        $dateDepart = $trajet->getDateDepart();
        $prix = $reservation->getCreditsUtilises();

        return $this->render('reservation/resume.html.twig', [
            'reservation' => $reservation,
            'trajet' => $trajet,
            'chauffeur' => $chauffeur,
            'adresseDepart' => $adresseDepart,
            'adresseArrivee' => $adresseArrivee,
            'dateDepart' => $dateDepart,
            'prix' => $prix,
        ]);
    }


    #[Route('/reservation/{id}/finalize', name: 'app_reservation_finalize', methods: ['POST'])]
    public function finalizeReservation(int $id, Request $request, ReservationRepository $reservationRepo, EntityManagerInterface $em): Response
    {

        if (!$this->isCsrfTokenValid('reservation_finalize' . $id, $request->request->get('_token'))) {
            $this->addFlash('error', 'Token invalide.');
            return $this->redirectToRoute('app_account_reservations');
        }

        /** @var Reservation|null $reservation */
        $reservation = $reservationRepo->find($id);
        if (!$reservation) {
            $this->addFlash('error', 'Réservation introuvable');
            return $this->redirectToRoute('app_account_reservations');
        }

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if ($reservation->getPassager()->getId() !== $user->getId()) {
            $this->addFlash('error', 'Vous ne pouvez pas valider cette réservation.');
            return $this->redirectToRoute('app_account_reservations');
        }

        $trajet = $reservation->getTrajet();
        if ($trajet->getStatut() !== 'terminé') {
            $this->addFlash('warning', 'Votre trajet n\'est pas signalé comme terminé par le chauffeur.');
            return $this->redirectToRoute('app_account_reservations');
        }

        if ($trajet->getStatut() !== 'terminé') {
            $this->addFlash('warning', 'Votre trajet n\'est pas signalé comme terminé par le chauffeur.');
            return $this->redirectToRoute('app_account_reservations');
        }


        if ($reservation->getStatut() === 'validé') {
            $this->addFlash('info', 'Vous avez déjà validé ce trajet.');
            return $this->redirectToRoute('app_account_reservations');
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

        $txCommission = new Transaction();
        $txCommission
            ->setUser($this->getUser())
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
        }

        $em->flush();

        $this->addFlash('success', 'Merci pour votre confirmation ! Le chauffeur a été payé, le voyage est terminé.');
        return $this->redirectToRoute('app_account');
    }



    #[Route('/reservation/{id}/report', name: 'app_reservation_report', methods: ['GET'])]
    public function reportReservation(int $id, ReservationRepository $reservationRepo): Response
    {

        /** @var Reservation|null $reservation */
        $reservation = $reservationRepo->find($id);
        if (!$reservation) {
            $this->addFlash('error', 'Réservation introuvable');
            return $this->redirectToRoute('app_account_reservations');
        }

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if ($reservation->getPassager()->getId() !== $user->getId()) {
            $this->addFlash('error', 'Vous ne pouvez pas signaler cette réservation.');
            return $this->redirectToRoute('app_account_reservations');
        }

        $trajet = $reservation->getTrajet();
        $chauffeur = $trajet->getChauffeur();
        $adresseDepart = $trajet->getAdresseDepart();
        $adresseArrivee = $trajet->getAdresseArrivee();
        $dateDepart = $trajet->getDateDepart();
        $prix = $reservation->getCreditsUtilises();

        return $this->render('reservation/report.html.twig', [
            'reservation' => $reservation,
            'trajet' => $trajet,
            'chauffeur' => $chauffeur,
            'adresseDepart' => $adresseDepart,
            'adresseArrivee' => $adresseArrivee,
            'dateDepart' => $dateDepart,
            'prix' => $prix,
        ]);
    }


    #[Route('/reservation/{id}/signal', name: 'app_reservation_signal', methods: ['POST'])]
    public function signalReservation(int $id, Request $request, ReservationRepository $reservationRepo, EntityManagerInterface $em): Response
    {

        if (!$this->isCsrfTokenValid('reservation_signal' . $id, $request->request->get('_token'))) {
            $this->addFlash('error', 'Token invalide.');
            return $this->redirectToRoute('app_account_reservations');
        }

        /** @var Reservation|null $reservation */
        $reservation = $reservationRepo->find($id);
        if (!$reservation) {
            $this->addFlash('error', 'Réservation introuvable');
            return $this->redirectToRoute('app_account_reservations');
        }

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if ($reservation->getPassager()->getId() !== $user->getId()) {
            $this->addFlash('error', 'Vous ne pouvez pas signaler cette réservation.');
            return $this->redirectToRoute('app_account_reservations');
        }


        $commentaire = trim($request->request->get('commentaire', ''));
        if (empty($commentaire)) {
            $this->addFlash('warning', 'Veuillez décrire le problème avant d\'envoyer.');
            return $this->redirectToRoute('app_reservation_report', ['id' => $id]);
        }

        $reservation->setStatut('signalé');
        $reservation->setCommentaireProbleme($commentaire);

        $em->persist($reservation);
        $em->flush();

        $this->addFlash('success', 'Votre signalement a bien été pris en compte. Notre équipe vous recontactera rapidement.');
        return $this->redirectToRoute('app_account_reservations');
    }
}
