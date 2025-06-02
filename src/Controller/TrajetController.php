<?php

namespace App\Controller;

use App\Entity\Trajet;
use App\Entity\Transaction;
use App\Form\TrajetType;
use App\Repository\ReservationRepository;
use App\Repository\TrajetRepository;
use App\Repository\VehiculeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TrajetController extends AbstractController
{
    #[Route('/account/trajet/new', name: 'account_trajet_new')]
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

        return $this->render('account/trajet_new.html.twig', [
            'form' => $form->createView(),
        ]);
    }





    #[Route('/trajet/{id}/annuler', name: 'app_trajet_annuler')]
    public function annulerTrajet(int $id, Request $request, EntityManagerInterface $em, TrajetRepository $trajetRepo, ReservationRepository $reservationRepo): Response
    {

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
}
