<?php

namespace App\Controller;

use App\Entity\Vehicule;
use App\Form\VehiculeForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class VehiculeController extends AbstractController
{
    #[Route('/account/vehicule_new', name: 'app_account_vehicule_new')]
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

        return $this->render('account/vehicule_new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
