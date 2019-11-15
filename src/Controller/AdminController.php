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
use App\Form\CategoryType;
use App\Form\TagsType;

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
        return $this->render('admin/user/manage.html.twig', [
            'users'=>$this->uR->findAll()
        ]);
    }

    /**
     * @Route("/admin/user/{id}/collection", name="userCollection", methods={"GET"})
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
        foreach($images as &$img)
        {
            $img->setImage(stream_get_contents($img->getImage()));
        }

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
        $this->em->flush();
        $this->addFlash('success', 'Image has been removed');

        return $this->redirectToRoute('imagesDash', []);
    }

    /**
     * @Route("/admin/images/new", name="loadImage", methods={"GET","POST"})
     */
    public function loadImage(Request $request)
    {
        // Call image upload form
        $uploadForm = $this->createForm(ImgLoadType::class);
        $uploadForm->handleRequest($request);

        if($uploadForm->isSubmitted() && $uploadForm->isValid())
        {
            // Get object based on form data
            $image = $uploadForm->getData();

            // Set date of creation and file place on server
            $image->setAddedAt(new \DateTime());
            $newFile = uniqid().'.'.$image->getImage()->guessExtension();

            try {
                $image->getImage()->move(
                    'gallery',
                    $newFile
                );

                $image->setImage($newFile);
            
                // Insert new object to database
                $this->em->persist($image);
                $this->em->flush();
                $this->addFlash('success', 'Image has been uploaded');
                return $this->redirectToRoute('imagesDash', []);
            }
            catch (\Throwable $th) {
                $this->addFlash('danger', 'Error occurred: '.$e);
                return $this->redirectToRoute('loadImage', []);
            }
        }

        return $this->render('admin/images/upload.html.twig', [
            'upload'=>$uploadForm->createView()
        ]);
    }

    /**
     * @Route("/admin/categories", name="categoriesDash", methods={"GET"})
     */
    public function categoriesDash()
    {
        return $this->render('admin/categories/manage.html.twig', [
            'categories'=>$this->cR->findAll()
        ]);
    }

    /**
     * @Route("/admin/categoires/add", name="newCategory", methods={"GET", "POST"})
     */
    public function newCategory(Request $request)
    {
        $catForm = $this->createForm(CategoryType::class);
        $catForm->handleRequest($request);

        if($catForm->isSubmitted() && $catForm->isValid())
        {
            $category = $catForm->getData();

            $this->em->persist($category);
            $this->em->flush();
            $this->addFlash('success', 'Category has been created');

            return $this->redirectToRoute('categoriesDash', []);
        }

        return $this->render('admin/categories/add.html.twig', [
            'catForm'=>$catForm->createView()
        ]);
    }

    /**
     * @Route("/admin/category/{id}/tags", name="catTags", methods={"GET"})
     */
    public function catTags($id)
    {
        return $this->render('admin/categories/tags.html.twig', [
            'cat'=>$this->cR->findBy(['id'=>$id])[0]
        ]);
    }

    /**
     * @Route("/admin/category/{id}/images", name="catImgs", methods={"GET"})
     */
    public function catImgs($id)
    {
        $cat = $this->cR->findBy(['id'=>$id])[0];
        foreach($cat->getGalleries() as &$img)
        {
            $img->setImage(stream_get_contents($img->getImage()));
        }

        return $this->render('admin/categories/imgs.html.twig', [
            'cat'=>$cat
        ]);
    }

    /**
     * @Route("/admin/category/{id}/remove", name="catDel", methods={"DELETE"})
     */
    public function catDel($id)
    {
        // Get category
        $cat = $this->cR->findBy(['id'=>$id])[0];
        // Remove images connected to this category
        foreach ($cat->getGalleries() as $img) {
            $this->em->remove($img);
        }
        // If any of tags connected to this category has only one related category remove it
        foreach ($cat->getTags() as $tag) {
            if(sizeof($tag->getCategories()) === 1)
            {
                $this->em->remove($tag);
            }
        }

        // Remove category itself
        $this->em->remove($cat);
        $this->em->flush();
        $this->addFlash('success', 'Category has been removed');

        return $this->redirectToRoute('categoriesDash', []);
    }

    /**
     * @Route("/admin/tags", name="tagsDash", methods={"GET"})
     */
    public function tagsDash()
    {
        return $this->render('admin/tags/manage.html.twig', [
            'tags'=>$this->tR->findAll()
        ]);
    }

    /**
     * @Route("/admin/tags/add", name="tagsAdd", methods={"GET", "POST"})
     */
    public function tagsAdd(Request $request)
    {
        $tagsForm = $this->createForm(TagsType::class);
        $tagsForm->handleRequest($request);

        if($tagsForm->isSubmitted() && $tagsForm->isValid())
        {
            $tag = $tagsForm->getData();

            $this->em->persist($tag);
            $this->em->flush();
            $this->addFlash('success', 'Tag has been created');

            return $this->redirectToRoute('tagsDash', []);
        }

        return $this->render('admin/tags/add.html.twig', [
            'tagForm'=>$tagsForm->createView()
        ]);
    }

    /**
     * @Route("/admin/tag/{id}", name="tagDel", methods={"DELETE"})
     */
    public function tagDel($id)
    {
        $this->em->remove($this->em->findBy(['id'=>$id])[0]);
        $this->em-flush();
        $this->addFlash('success', 'Tag has been removed');

        return $this->redirectToRoute('tagsDash', []);
    }
}
