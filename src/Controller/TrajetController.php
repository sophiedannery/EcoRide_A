<?php

namespace App\Controller;

use App\Entity\Trajet;
use App\Entity\Transaction;
use App\Form\TrajetType;
use App\Repository\ReservationRepository;
use App\Repository\TrajetRepository;
use App\Repository\VehiculeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class TrajetController extends AbstractController
{

    #[Route('/trajet/trajet_account', name: 'app_trajet_account')]
    #[IsGranted('ROLE_USER')]
    public function trajets(Request $request, EntityManagerInterface $em, ReservationRepository $reservation_repository, TrajetRepository $trajet_repository): Response
    {

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $vehicules = $user->getVehicules();
        $history = $reservation_repository->findHistoryByUser($user->getId());
        $driverTrips = $trajet_repository->findTripsByDriver($user->getId());

        foreach ($driverTrips as &$trip) {
            $tripId = $trip['id_trajet'];
            $passagers = $reservation_repository->findPassengerPseudoByTrajet($tripId);
            $trip['passagers'] = $passagers;
        }



        return $this->render('trajet/trajet_account.html.twig', [
            'history' => $history,
            'driverTrips' => $driverTrips,
            'vehicules' => $vehicules,
        ]);
    }


    #[Route('/trajet/trajet_historique', name: 'app_trajet_historique')]
    #[IsGranted('ROLE_USER')]
    public function trajetsOld(Request $request, EntityManagerInterface $em, ReservationRepository $reservation_repository, TrajetRepository $trajet_repository): Response
    {

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $vehicules = $user->getVehicules();
        $history = $reservation_repository->findHistoryByUser($user->getId());
        $driverTrips = $trajet_repository->findTripsByDriver($user->getId());

        foreach ($driverTrips as &$trip) {
            $tripId = $trip['id_trajet'];
            $passagers = $reservation_repository->findPassengerPseudoByTrajet($tripId);
            $trip['passagers'] = $passagers;
        }



        return $this->render('trajet/trajet_historique.html.twig', [
            'history' => $history,
            'driverTrips' => $driverTrips,
            'vehicules' => $vehicules,
        ]);
    }



    #[Route('/trajet/trajet_new', name: 'app_trajet_new')]
    public function new(Request $request, EntityManagerInterface $em, VehiculeRepository $vehiculeRepo): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if (!in_array($user->getStatut(), ['chauffeur', 'passager_chauffeur'], true)) {
            $this->addFlash('error', 'Seuls les chauffeurs peuvent créer un trajet.');
            return $this->redirectToRoute('app_account');
        }

        $trajet = new Trajet();

        $form = $this->createForm(TrajetType::class, $trajet, [
            'user' => $user,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if (null === $trajet->getVehicule()) {
                $this->addFlash('warning', 'Vous devez d’abord ajouter un véhicule ou le sélectionner.');
                return $this->redirectToRoute('app_account_vehicule_new');
            }

            // Ajuster prix net pour la plateforme ? (ou stocker la commission ailleurs)
            // e.g. $trajet->setPrix($trajet->getPrix() - 2);

            $trajet->setStatut('confirmé');

            $trajet->setEnergie($trajet->getVehicule()->getEnergie());

            $trajet->setChauffeur($user)
                ->setPlacesRestantes($trajet->getVehicule()->getPlacesDisponibles());

            $em->persist($trajet);
            $em->flush();

            $this->addFlash('success', 'Trajet créé avec succès !');
            return $this->redirectToRoute('app_account');
        }

        return $this->render('trajet/trajet_new.html.twig', [
            'form' => $form->createView(),
        ]);
    }





    #[Route('/trajet/{id}/annuler', name: 'app_trajet_annuler')]
    public function annulerTrajet(
        int $id,
        Request $request,
        EntityManagerInterface $em,
        TrajetRepository $trajetRepo,
        ReservationRepository $reservationRepo,
        MailerInterface $mailer
    ): Response {

        if (!$this->isCsrfTokenValid('cancel_trajet' . $id, $request->request->get('_token'))) {
            $this->addFlash('error', 'Token invalide.');
            return $this->redirectToRoute('app_account');
        }

        $trajet = $trajetRepo->find($id);
        if (!$trajet) {
            $this->addFlash('error', 'Trajet introuvable.');
            return $this->redirectToRoute('app_account');
        }

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if ($trajet->getChauffeur()->getId() !== $user->getId()) {
            $this->addFlash('error', 'Vous ne pouvez pas annuler ce trajet.');
            return $this->redirectToRoute('app_account');
        }

        $reservations = $reservationRepo->findBy(['trajet' => $trajet]);

        foreach ($reservations as $reservation) {
            $passager = $reservation->getPassager();
            $creditsUtilises = $reservation->getCreditsUtilises();

            $passager->setCredits($passager->getCredits() + $creditsUtilises);

            $email = (new TemplatedEmail())
                ->from('team.ecoride@gmail.com')
                ->to($passager->getEmail())
                ->subject('Trajet annulé')
                ->htmlTemplate('emails/annulation-trajet.html.twig')
                ->context([
                    'pseudo' => $passager->getPseudo(),
                    'adresse_depart' => $trajet->getAdresseDepart(),
                    'adresse_arrivee' => $trajet->getAdresseArrivee(),
                    'date_depart' => $trajet->getDateDepart()->format('d/m/Y H:i'),
                    'credits' => $creditsUtilises,
                ]);

            try {
                $mailer->send($email);
            } catch (Exception $e) {
                $this->addFlash('error', 'Erreur lors de l\'envoi du mail à ' . $passager->getEmail());
            }

            $transaction = new Transaction();
            $transaction
                ->setUser($user)
                ->setTrajet($trajet)
                ->setMontant($creditsUtilises)
                ->setType('remboursement_passager_annulation_chauffeur');
            $em->persist($transaction);

            foreach ($reservation->getAvis() as $avi) {
                $em->remove($avi);
            }

            $em->remove($reservation);
        }

        $trajet->setStatut('annulé');

        $em->flush();
        $this->addFlash('success', 'Le trajet a été annulé et tous les passagers remboursés.');

        return $this->redirectToRoute('app_account');
    }





    #[Route('/account/trajet/{id}/demarrer', name: 'app_trajet_demarrer', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function demarrer(int $id, Request $request, EntityManagerInterface $em, TrajetRepository $trajetRepo): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $trajet = $trajetRepo->find($id);

        if (!$trajet) {
            $this->addFlash('error', 'Trajet introuvable');
            return $this->redirectToRoute('app_account');
        }

        if ($trajet->getChauffeur()->getId() !== $user->getId()) {
            $this->addFlash('error', 'Vous n\'êtes pas autorisé à démarrer ce trajet.');
            return $this->redirectToRoute('app_account');
        }

        $aujourdhui = new \DateTimeImmutable('today');
        if ($trajet->getStatut() !== 'confirmé' || $trajet->getDateDepart()->format('Y-m-d') !== $aujourdhui->format('Y-m-d')) {
            $this->addFlash('warning', 'Ce trajet n\'est pas encore disponible pour démarrage.');
            return $this->redirectToRoute('app_account');
        }


        $trajet->setStatut('en_cours');
        $em->flush();
        $this->addFlash('success', 'Trajet démarré. Bon voyage !');

        return $this->redirectToRoute('app_account');
    }




    #[Route('/account/trajet/{id}/arriver', name: 'app_trajet_arriver', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function arriver(int $id, Request $request, EntityManagerInterface $em, TrajetRepository $trajetRepo, ReservationRepository $reservationRepo): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $trajet = $trajetRepo->find($id);

        if (!$trajet) {
            $this->addFlash('error', 'Trajet introuvable');
            return $this->redirectToRoute('app_account');
        }

        if ($trajet->getChauffeur()->getId() !== $user->getId()) {
            $this->addFlash('error', 'Vous n\'êtes pas autorisé à clore ce trajet.');
            return $this->redirectToRoute('app_account');
        }

        if ($trajet->getStatut() !== 'en_cours') {
            $this->addFlash('warning', 'Impossible de clore un trajet qui n\'est pas en cours.');
            return $this->redirectToRoute('app_account');
        }


        $trajet->setStatut('terminé');
        $em->flush();

        $this->addFlash('success', 'Trajet terminé. Les passagers peuvent maintenant valider.');

        return $this->redirectToRoute('app_account');
    }
}
