<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Services\LoginStatus;
use App\Form\SearchType;
use App\Repository\GalleryRepository;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\EntityManagerInterface;

class AppController extends AbstractController
{
    private $gR;
    private $session;
    private $ls;
    private $pag;
    private $uR;
    private $em;

    public function __construct(EntityManagerInterface $em, LoginStatus $ls, SessionInterface $session, UserRepository $uR, GalleryRepository $gR, PaginatorInterface $pag)
    {
        $this->pag = $pag;
        $this->gR = $gR;
        $this->uR = $uR;
        $this->em = $em;
        $this->session = $session;
        $this->ls = $ls->checkLoginStatus($session);
    }

    /**
     * @Route("/", name="homepage", methods={"GET"})
     */
    public function index(Request $request)
    {
        $images = $this->gR->findBy(array(), array('addedAt'=>'DESC'), 500);
        foreach($images as &$img)
        {
            $img->setImage(stream_get_contents($img->getImage()));
        }

        return $this->render('app/homepage.html.twig', [
            'pagination'=>$this->pag->paginate(
                $images, $request->query->getInt('page', 1), 35)
        ]);
    }

    /**
     * @Route("/search", name="searched", methods={"GET"})
     */
    public function searched()
    {
        return $this->render('app/searched.html.twig', []);
    }

    /**
     * @Route("/image/{id}", name="imgDisp", methods={"GET"})
     */
    public function imgDisp($id)
    {
        $img = $this->gR->findBy(['id'=>$id]);
        if ($img) {
            $img = $img[0];
            $img->setImage(stream_get_contents($img->getImage()));
        }
        if($this->ls)
        {
            $this->viewMod($id);
        }

        return $this->render('app/imgDisp.html.twig', [
            'img'=>$img
        ]);
    }

    /**
     * @Route("/image/{id}/like", name="likeImg", methods={"POST"})
     */
    public function likeImg($id)
    {
        $img = $this->gR->findBy(['id'=>$id])[0];
        $user = $this->uR->findBy(['id'=>$this->session->get('user')->getId()])[0];
        if(!$img->getLikes()->contains($user))
        {
            $img->addLike($user);
        }
        else
        {
            $img->removeLike($user);
        }
        $this->em->flush();

        return $this->redirectToRoute('imgDisp', ['id'=>$id]);
    }

    /**
     * @Route("/image/{id}/save", name="saveImg", methods={"POST"})
     */
    public function saveImg($id)
    {
        $img = $this->gR->findBy(['id'=>$id])[0];
        $user = $this->uR->findBy(['id'=>$this->session->get('user')->getId()])[0];
        if(!$img->getSaves()->contains($user))
        {
            $img->addSave($user);
        }
        else
        {
            $img->removeSave($user);
        }
        $this->em->flush();

        return $this->redirectToRoute('imgDisp', ['id'=>$id]);
    }
    
    private function viewMod($id)
    {
        $img = $this->gR->findBy(['id'=>$id])[0];
        $user = $this->uR->findBy(['id'=>$this->session->get('user')->getId()])[0];

        if(!$img->getViews()->contains($user))
        {
            $img->addView($user);
            $this->em->flush();
        }
    }
}
