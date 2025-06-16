<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email as MimeEmail;
use Symfony\Component\Routing\Attribute\Route;

final class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact', methods: ['GET', 'POST'])]
    public function contact(Request $request, MailerInterface $mailer): Response
    {

        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $email = (new TemplatedEmail())
                ->from($data['email'])
                ->to('team.ecoride@gmail.com')
                ->subject('[EcoRide] Message : ' . $data['subject'])
                ->htmlTemplate('emails/contact.html.twig')
                ->context([
                    'name' => $data['name'],
                    'sender_email' => $data['email'],
                    'message' => $data['message'],
                ]);

            $mailer->send($email);

            $this->addFlash('success', 'Votre message a bien été envoyé.');
            return $this->redirectToRoute('app_contact');
        }



        return $this->render('contact/index.html.twig', [
            'contactForm' => $form->createView(),
        ]);
    }
}
