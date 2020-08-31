<?php

namespace App\Controller;

use App\Repository\CategoriesRepository;
use App\Repository\GalleryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    public function __construct(GalleryRepository $gr)
    {
        $this->gr = $gr;
    }

    /**
     * @Route("/", name="homepage", methods={"GET"})
     */
    public function index(PaginatorInterface $paginator, Request $request)
    {
        return $this->render('app/homepage.html.twig', [
            'gallery' => $paginator->paginate($this->gr->findBy([], ['addedAt' => 'DESC'], 105), $request->query->getInt('page', 1), 15)
        ]);
    }

    /**
     * @Route("/explore/{attr}", name="explore", methods={"GET"}, defaults={"attr": ""})
     */
    public function explore(string $attr, CategoriesRepository $cr, PaginatorInterface $paginator, Request $request)
    {
        if ($attr == "")
            return $this->render('app/explore/categories.html.twig', [
                'categories' => $cr->findBy([], ['Name' => 'ASC'])
            ]);
        else {
            return $this->render('app/explore/explore.html.twig', [
                'results' => $paginator->paginate($attr == "all" ? $this->gr->findAll() : $this->gr->getMatchingResults($attr), $request->query->getInt('page', 1), 20)
            ]);
        }
    }

    /**
     * @Route("/check-view/{id}", methods={"POST"})
     */
    public function checkView(int $id, EntityManagerInterface $em, bool $viewed = false)
    {
        $post = $this->gr->findOneBy(['id' => $id]);
        if ($post) {
            foreach ($post->getViews() as $v) {
                if ($v->getId() == $this->getUser()->getId()) {
                    $viewed = true;
                    break;
                }
            }
            if (!$viewed) {
                $post->addView($this->getUser());
                $em->flush();
                return new Response("Successfully added to viewed", 200);
            } else {
                return new Response("Seen already", 202);
            }
        } else {
            return new Response("There is no such object matching requirements.", 404);
        }
    }

    /**
     * @Route("/like/{id}", name="like", methods={"GET"})
     */
    public function like(int $id, EntityManagerInterface $em, bool $liked = false, Request $request)
    {
        $post = $this->gr->findOneBy(['id' => $id]);
        if ($post) {
            foreach ($post->getLikes() as $l) {
                if ($l->getId() == $this->getUser()->getId()) {
                    $liked = true;
                    break;
                }
            }

            !$liked ? $post->addLike($this->getUser()) : $post->removeLike($this->getUser());
            $em->flush();
            return $this->redirect($request->query->get('ref'));
        } else {
            throw new \Exception("No object found. Refresh page and try again", 404);
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
