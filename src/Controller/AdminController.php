<?php

namespace App\Controller;

use App\Form\CategoryType;
use App\Form\CEditType;
use App\Repository\CategoriesRepository;
use App\Repository\GalleryRepository;
use App\Repository\ReportsRepository;
use App\Repository\TagsRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\QueryException;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    public function __construct(UserRepository $ur, TagsRepository $tr, CategoriesRepository $cr, GalleryRepository $gr, ReportsRepository $rr, SessionInterface $session, EntityManagerInterface $em)
    {
        $this->ur = $ur;
        $this->tr = $tr;
        $this->cr = $cr;
        $this->gr = $gr;
        $this->rr = $rr;
        $this->session = $session;
        $this->em = $em;
    }

    public function __call($method, $args)
    {
        return call_user_func_array(
            $this->checkPending(),
            $args
        );
    }

    private function checkPending()
    {
        $pending = $this->rr->getPendingAmount();
        if ($pending > 0) $this->session->set('pending', $pending);
        else if ($pending < 1 && $this->session->get('pending')) $this->session->remove('pending');
    }

    /**
     * @Route("/admin/dashboard", name="dashboard", methods={"GET"})
     */
    public function dashboard()
    {
        return $this->render('admin/dashboard.html.twig', [
            'users' => $this->ur->getNoUsers(),
            'tags' => $this->tr->getNoTags(),
            'categories' => $this->cr->getNoCategories(),
            'uploads' => $this->gr->getNoUploads()
        ]);
    }

    /**
     * @Route("/admin/reports", name="aReports", methods={"GET"})
     */
    public function aReports(Request $request)
    {
        return $this->render('admin/reports.html.twig', [
            'reports' => $request->query->get('id') ? $this->rr->findBy(['user' => $request->query->get('id')]) : $this->rr->findAll()
        ]);
    }

    /**
     * @Route("/admin/users", name="aUsers", methods={"GET"})
     */
    public function aUsers()
    {
        return $this->render('admin/users.html.twig', [
            'users' => $this->ur->getUsers()
        ]);
    }

    /**
     * @Route("/admin/user/{id}/disable", name="aUDisable", methods={"GET"})
     */
    public function aUDisable(int $id)
    {
        try {
            $user = $this->ur->findOneBy(['id' => $id]);
            $user->setIsDisabled(!$user->getIsDisabled());

            $this->em->flush();

            return $this->redirectToRoute('aUsers', []);
        } catch (QueryException $e) {
            throw new Exception("Problem appeared during changing. Try again. Error code: {$e->getMessage()}", 500);
        }
    }

    /**
     * @Route("/admin/user/{id}/promote", name="aUPromote", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function aUPromote(int $id)
    {
        try {
            $user = $this->ur->findOneBy(['id' => $id]);
            if (in_array("ROLE_MODERATOR", $user->getRoles())) $user->setRoles(['ROLE_USER', 'ROLE_MODERATOR', 'ROLE_ADMIN']);
            else if (!in_array("ROLE_MODERATOR", $user->getRoles())) $user->setRoles(['ROLE_USER', 'ROLE_MODERATOR']);

            $this->em->flush();

            return $this->redirectToRoute('aUsers', []);
        } catch (QueryException $e) {
            throw new Exception("Problem appeared during changing. Try again. Error code: {$e->getMessage()}", 500);
        }
    }

    /**
     * @Route("/admin/user/{id}/demote", name="aUDemote", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function aUDemote(int $id)
    {
        try {
            $user = $this->ur->findOneBy(['id' => $id]);
            if (!in_array("ROLE_ADMIN", $user->getRoles())) $user->setRoles(['ROLE_USER']);
            else if (in_array("ROLE_ADMIN", $user->getRoles())) $user->setRoles(['ROLE_USER', 'ROLE_MODERATOR']);

            $this->em->flush();

            return $this->redirectToRoute('aUsers', []);
        } catch (QueryException $e) {
            throw new Exception("Problem appeared during changing. Try again. Error code: {$e->getMessage()}", 500);
        }
    }

    /**
     * @Route("/admin/uploads", name="aUploads", methods={"GET"})
     */
    public function aUploads(PaginatorInterface $paginator, Request $request)
    {
        return $this->render('admin/uploads.html.twig', [
            'uploads' => $paginator->paginate($this->gr->findAll(), $request->query->getInt('page', 1), 15)
        ]);
    }

    /**
     * @Route("/admin/upload/{id}/remove", name="aURemove", methods={"GET"})
     */
    public function aURemove(int $id)
    {
        try {
            $post = $this->gr->findOneBy(['id' => $id]);
            $this->em->remove($post);
            $this->em->flush();
            return $this->redirectToRoute('aUploads', [
                'page' => 1
            ]);
        } catch (QueryException $e) {
            throw new Exception("Something went wrong try again. Error: {$e->getMessage()}", 500);
        }
    }

    /**
     * @Route("/admin/categories", name="aCategories", methods={"GET"})
     */
    public function aCategories(PaginatorInterface $paginator, Request $request)
    {
        return $this->render('admin/categories/categories.html.twig', [
            'categories' => $paginator->paginate($this->cr->findAll(), $request->query->getInt('page', 1), 15)
        ]);
    }

    /**
     * @Route("/admin/categories/create", name="aCNew", methods={"GET", "POST"})
     */
    public function aCNew(Request $request)
    {
        $create = $this->createForm(CategoryType::class);
        $create->handleRequest($request);

        if ($create->isSubmitted() && $create->isValid()) {
            try {
                $data = $create->getData();

                $cat = new \App\Entity\Categories;
                $cat->setName($data['Name']);
                foreach ($data['Tags'] as $t) $cat->addTag($t);

                $this->em->persist($cat);
                $this->em->flush();

                return $this->redirectToRoute('aCategories', [
                    'page' => 1
                ]);
            } catch (QueryException $e) {
                throw new Exception("Something went wrong try again. Error: {$e->getMessage()}", 500);
            }
        }

        return $this->render('admin/categories/new.html.twig', [
            'create' => $create->createView()
        ]);
    }

    /**
     * @Route("/admin/category/{id}/edit", name="aCEdit", methods={"GET", "POST"})
     */
    public function aCEdit(int $id, Request $request)
    {
        $edit = $this->createForm(CEditType::class, null, ['id' => $id]);
        $edit->handleRequest($request);
        $cat = $this->cr->findOneBy(['id' => $id]);

        if ($edit->isSubmitted() && $edit->isValid()) {
            if ($cat) {
                try {
                    $data = $edit->getData();

                    $cat->setName($data['Name']);
                    $cat->getTags()->clear();
                    foreach ($data['Tags'] as $t) $cat->addTag($t);

                    $this->em->flush();

                    return $this->redirectToRoute('aCategories', [
                        'page' => 1
                    ]);
                } catch (QueryException $e) {
                    throw new Exception("Something went wrong try again. Error: {$e->getMessage()}", 500);
                }
            } else throw new Exception("Category not found", 404);
        }

        return $this->render('admin/categories/edit.html.twig', [
            'edit' => $edit->createView(),
            'catTags' => $cat->getTags()
        ]);
    }

    /**
     * @Route("/admin/category/{id}/remove", name="aCRemove", methods={"GET"})
     */
    public function aCRemove(int $id)
    {
        try {
            $category = $this->cr->findOneBy(['id' => $id]);
            $this->em->remove($category);
            $this->em->flush();
            return $this->redirectToRoute('aCategories', [
                'page' => 1
            ]);
        } catch (QueryException $e) {
            throw new Exception("Something went wrong try again. Error: {$e->getMessage()}", 500);
        }
    }

    /**
     * @Route("/admin/tags", name="aTags", methods={"GET"})
     */
    public function aTags(PaginatorInterface $paginator, Request $request)
    {
        return $this->render('admin/tags/tags.html.twig', [
            'tags' => $paginator->paginate($this->tr->findAll(), $request->query->getInt('page', 1), 15)
        ]);
    }
}
