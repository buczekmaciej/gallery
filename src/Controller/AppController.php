<?php

namespace App\Controller;

use App\Repository\CategoriesRepository;
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

    /**
     * @Route("/explore/{attr}", name="explore", methods={"GET"}, defaults={"attr": ""})
     */
    public function explore(string $attr, CategoriesRepository $cr, GalleryRepository $gr, PaginatorInterface $paginator, Request $request)
    {
        if ($attr == "")
            return $this->render('app/explore/categories.html.twig', [
                'categories' => $cr->findBy([], ['Name' => 'ASC'])
            ]);
        else {
            return $this->render('app/explore/explore.html.twig', [
                'results' => $paginator->paginate($attr == "all" ? $gr->findAll() : $gr->getMatchingResults($attr), $request->query->getInt('page', 1), 20)
            ]);
        }
    }

    /**
     * @Route("/upload-new", name="upload", methods={"GET", "POST"})
     */
    public function upload()
    {
        return $this->render('app/upload.html.twig', []);
    }
}
