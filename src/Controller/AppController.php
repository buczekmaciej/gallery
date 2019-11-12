<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AppController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(SessionInterface $session)
    {dump($session->get('user'));
        return $this->render('app/homepage.html.twig', []);
    }
}
