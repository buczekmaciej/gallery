<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Services\LoginStatus;
use App\Form\SearchType;
use App\Repository\GalleryRepository;
use Knp\Component\Pager\PaginatorInterface;

class AppController extends AbstractController
{
    private $gR;
    private $session;
    private $ls;
    private $pag;

    public function __construct(LoginStatus $ls, SessionInterface $session, GalleryRepository $gR, PaginatorInterface $pag)
    {
        $this->pag = $pag;
        $this->gR = $gR;
        $this->session = $session;
        $this->ls = $ls->checkLoginStatus($session);
    }

    /**
     * @Route("/", name="homepage")
     */
    public function index(Request $request)
    {
        return $this->render('app/homepage.html.twig', [
            'pagination'=>$this->pag->paginate(
                $this->gR->findBy(array(), array('addedAt'=>'DESC'), 500), $request->query->getInt('page', 1), 35)
        ]);
    }

    /**
     * @Route("/search", name="searched")
     */
    public function searched()
    {

        return $this->render('app/searched.html.twig', []);
    }
}
