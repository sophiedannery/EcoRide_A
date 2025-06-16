<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

class MailerTestController extends AbstractController
{
    #[Route('/mailer/test', name: 'app_mailer_test')]
    public function sendEmail(MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('team.ecoride@gmail.com')
            ->to('dannery.sophie@gmail.com')
            ->subject('Test Mail')
            ->html('<h1>Hello World !</h1><p>Si tu vois ça, t\'es un génie ! ✨</p>');

        $mailer->send($email);

        return new Response('✅ Email envoyé avec succès !');
    }
}
