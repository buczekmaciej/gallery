<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserRepository;
use App\Repository\GalleryRepository;
use App\Repository\TagsRepository;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\ImgLoadType;

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
        // Get all images
        $images = $this->gR->findAll();

        return $this->render('admin/images/manage.html.twig', [
            'images'=>$images
        ]);
    }

    /**
     * @Route("/admin/image/{id}", name="deleteImage", methods={"DELETE"})
     */
    public function deleteImage($id)
    {
        // Remove specific image
        $this->em->remove($this->gR->findBy(['id'=>$id])[0]);
        $this->addFlash('success', 'Image has been removed');

        return $this->redirectToRoute('imagesDash', []);
    }

    /**
     * @Route("/admin/images/new", name="loadImage", methods={"GET","POST"})
     */
    public function loadImage(Request $request)
    {
        $uploadForm = $this->createForm(ImgLoadType::class);
        $uploadForm->handleRequest($request);

        if($uploadForm->isSubmitted() && $uploadForm->isValid())
        {
            $image = $uploadForm->getData();

            $image->setAddedAt(new \DateTime());
            $newFile = uniqid().$image->getImage()->guessExtension();

            try {
                $image->getImage()->move(
                    'gallery',
                    $newFile
                );

                $image->setImage($newFile);
            } catch (\Throwable $th) {
                $this->addFlash('danger', 'Error occurred: '.$e);
                return $this->redirectToRoute('loadImage', []);
            }
            
            $this->em->persist($image);
            $this->addFlash('success', 'Image has been uploaded');
            return $this->redirectToRoute('imagesDash', []);
        }

        return $this->render('admin/images/upload.html.twig', [
            'upload'=>$uploadForm->createView()
        ]);
    }
}
