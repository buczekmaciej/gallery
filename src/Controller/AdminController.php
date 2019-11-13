<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use App\Repository\GalleryRepository;
use App\Repository\TagsRepository;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;

class AdminController extends AbstractController
{
    private $uR;
    private $gR;
    private $cR;
    private $tR;
    private $em;

    public function __construct(EntityManagerInterface $em, CategoriesRepository $cR, TagsRepository $tR, GalleryRepository $gR, UserRepository $uR)
    {
        $this->uR = $uR;
        $this->gR = $gR;
        $this->tR = $tR;
        $this->cR = $cR;
        $this->em = $em;
    }

    /**
     * @Route("/admin/dashboard", name="dashboard", methods={"GET"})
     */
    public function dashboard()
    {
        return $this->render('admin/dashboard.html.twig', []);
    }

    /**
     * @Route("/admin/users", name="usersDash", methods={"GET"})
     */
    public function usersDash()
    {
        // Get all users
        $users = $this->uR->findAll();

        return $this->render('admin/user/manage.html.twig', [
            'users'=>$users
        ]);
    }

    /**
     * @Route("/admin/user/{id}", name="userCollection", methods={"GET"})
     */
    public function userCollection($id)
    {
        // Get specific user and translate file name from stream
        $user = $this->uR->findBy(['id'=>$id])[0];
        foreach($user->getCollection() as &$coll)
        {
            $coll->setImage(stream_get_contents($coll->getImage()));
        }

        return $this->render('admin/user/collection.html.twig', [
            'user'=>$user
        ]);
    }

    /**
     * @Route("/admin/images", name="imagesDash", methods={"GET"})
     */
    public function imagesDash()
    {
        $images = $this->gR->findAll();

        return $this->render('admin/images/manage.html.twig', [
            'images'=>$images
        ]);
    }
}
