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
     * @Route("/admin/dashboard", name="dashboard")
     */
    public function dashboard()
    {
        return $this->render('admin/dashboard.html.twig', []);
    }

    /**
     * @Route("/admin/users", name="usersDash")
     */
    public function usersDash()
    {
        $users = $this->uR->findAll();

        return $this->render('admin/user/manage.html.twig', [
            'users'=>$users
        ]);
    }
}
