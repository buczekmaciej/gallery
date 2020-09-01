<?php

namespace App\Controller;

use App\Repository\CategoriesRepository;
use App\Repository\GalleryRepository;
use App\Repository\TagsRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin/dashboard", name="dashboard", methods={"GET"})
     */
    public function dashboard(UserRepository $ur, TagsRepository $tr, CategoriesRepository $cr, GalleryRepository $gr)
    {
        return $this->render('admin/dashboard.html.twig', [
            'users' => $ur->getNoUsers(),
            'tags' => $tr->getNoTags(),
            'categories' => $cr->getNoCategories(),
            'uploads' => $gr->getNoUploads()
        ]);
    }
}
