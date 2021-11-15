<?php

namespace App\Controller;

use App\Form\ContactFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
{
    /**
     * @Route("/contact", name="AppContact")
     * @throws TransportExceptionInterface
     */
    public function contact(Request $request, MailerInterface $mailer){

        $form = $this->createForm(ContactFormType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $email = (new Email())
                ->from($form->get('email')->getData())
                ->to(new Address('contact.bloutime@gmail.com', "KnowBook"))
                ->subject($form->get('subject')->getData())
                ->text($form->get('content')->getData());
            $mailer->send($email);

            return $this->redirectToRoute("AppHome");

        }

        return $this->render('front/contact.html.twig', [
            'contactForm'=>$form->createView()
        ]);
    }
}
