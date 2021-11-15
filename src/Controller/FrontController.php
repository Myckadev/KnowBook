<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
{
    /**
     * @Route("/contact", name="AppContact")
     */
    public function contact(){

        return $this->render('front/contact.html.twig', [

        ]);
    }
}
