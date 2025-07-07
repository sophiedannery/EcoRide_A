<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Form\AvisFormType;
use App\Entity\Reservation;
use App\Repository\AvisRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class AvisController extends AbstractController
{

    #[Route('/avis/avis_account', name: 'app_avis_account')]
    #[IsGranted('ROLE_USER')]
    public function mesAvis(AvisRepository $avisRepo): Response
    {

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $avisRecus = $avisRepo->findAvisByChauffeur($user->getId());

        return $this->render('avis/avis_account.html.twig', [
            'avisRecus' => $avisRecus,
        ]);
    }


    #[Route('/avis/new/{reservationId}', name: 'app_avis_new')]
    public function new(
        int $reservationId,
        ReservationRepository $reservationRepo,
        Request $request,
        EntityManagerInterface $em
    ): Response {

        /** @var Reservation|null $reservation */

        $reservation = $reservationRepo->find($reservationId);

        if (!$reservation) {
            $this->addFlash('error', 'Réservation introuvable.');
            return $this->redirectToRoute('app_reservation_account');
        }

        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        if ($reservation->getPassager()->getId() !== $user->getId()) {
            $this->addFlash('error', 'Vous n\'êtes pas autorisé à laisser un avis pour cette réservation.');
            return $this->redirectToRoute('app_reservation_account');
        }

        $trajet = $reservation->getTrajet();
        if ($trajet->getStatut() !== 'trajet_termine') {
            $this->addFlash('warning', 'Vous ne pouvez laisser un avis que lorsqu’un trajet est terminé.');
            return $this->redirectToRoute('app_reservation_account');
        }

        $existingAvis = $em->getRepository(Avis::class)->findOneBy(['reservation' => $reservation]);
        if ($existingAvis) {
            $this->addFlash('info', 'Vous avez déjà laissé un avis pour cette réservation.');
            return $this->redirectToRoute('app_reservation_account');
        }

        $avis = new Avis();
        $avis->setReservation($reservation);
        $avis->setDateCreation(new \DateTime());

        $form = $this->createForm(AvisFormType::class, $avis);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $avis = $form->getData();

            if (empty($avis->getCommentaire())) {
                $avis->setStatutValidation('validé');
                $this->addFlash('success', 'Votre avis a été enregistré.');
            } else {
                $avis->setStatutValidation('en_attente');
                $this->addFlash('success', 'Votre avis est en attente de validation.');
            }

            $em->persist($avis);
            $em->flush();

            return $this->redirectToRoute('app_reservation_account');
        }

        return $this->render('avis/new.html.twig', [
            'avisForm'       => $form->createView(),
            'reservation'    => $reservation,
            'chauffeurPseudo' => $trajet->getChauffeur()->getPseudo(),
        ]);
    }
}
