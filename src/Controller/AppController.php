<?php

namespace App\Controller;

use App\Repository\GalleryRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    /**
     * @Route("/", name="homepage", methods={"GET"})
     */
    public function index(PaginatorInterface $paginator, GalleryRepository $gr, Request $request)
    {
        return $this->render('app/homepage.html.twig', [
            'gallery' => $paginator->paginate($gr->findBy([], ['addedAt' => 'DESC'], 105), $request->query->getInt('page', 1), 15)
        ]);
    }
}
