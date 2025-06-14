<?php

namespace App\Controller;

use App\Entity\Vehicule;
use App\Form\VehiculeForm;
use App\Repository\ReservationRepository;
use App\Repository\TrajetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class VehiculeController extends AbstractController
{
    #[Route('/vehicule/vehicule_new', name: 'app_vehicule_new')]
    #[IsGranted('ROLE_USER')]
    public function addVehicule(Request $req, EntityManagerInterface $em): Response
    {
        $vehicule = new Vehicule();
        $form = $this->createForm(VehiculeForm::class, $vehicule);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var \App\Entity\User $user */
            $user = $this->getUser();
            $vehicule->setUser($user);

            $em->persist($vehicule);
            $em->flush();

            $this->addFlash('success', 'Véhicule ajouté avec succès !');

            return $this->redirectToRoute('app_account');
        }

        return $this->render('vehicule/vehicule_new.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/vehicule/vehicule_account', name: 'app_vehicule_account')]
    #[IsGranted('ROLE_USER')]
    public function vehicules(Request $request, EntityManagerInterface $em, ReservationRepository $reservation_repository, TrajetRepository $trajet_repository): Response
    {

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $vehicules = $user->getVehicules();

        return $this->render('vehicule/vehicule_account.html.twig', [
            'vehicules' => $vehicules,
        ]);
    }
}
