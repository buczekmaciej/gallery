<?php

namespace App\Controller;

use App\Form\CategoryType;
use App\Form\CEditType;
use App\Form\EReasonType;
use App\Form\NewReasonType;
use App\Form\TagsType;
use App\Form\TEditType;
use App\Repository\CategoriesRepository;
use App\Repository\GalleryRepository;
use App\Repository\ReasonsRepository;
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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    public function __construct(UserRepository $ur, TagsRepository $tr, CategoriesRepository $cr, GalleryRepository $gr, ReportsRepository $rr, ReasonsRepository $rsr, SessionInterface $session, EntityManagerInterface $em)
    {
        $this->ur = $ur;
        $this->tr = $tr;
        $this->cr = $cr;
        $this->gr = $gr;
        $this->rr = $rr;
        $this->rsr = $rsr;
        $this->session = $session;
        $this->em = $em;
    }

    private function checkPending()
    {
        if (in_array("ROLE_ADMIN", $this->getUser()->getRoles())) {
            $pending = $this->rr->getPendingAmount();
            if ($pending > 0) $this->session->set('pending', $pending);
            else if ($pending < 1 && $this->session->get('pending')) $this->session->remove('pending');
        }
    }

    /**
     * @Route("/admin/dashboard", name="dashboard", methods={"GET"})
     */
    public function dashboard()
    {
        $this->checkPending();

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
    public function aReports()
    {
        $this->checkPending();

        return $this->render('admin/reports/reports.html.twig', [
            'reports' => $this->rr->getReports()
        ]);
    }

    /**
     * @Route("/admin/report/{id}", name="aReport", methods={"GET", "POST"})
     */
    public function aReport(int $id)
    {
        $this->checkPending();

        $report = $this->rr->findOneBy(['id' => $id]);
        if ($report->getStatus() == 'Reported') {
            $report->setStatus('Open');
            $this->em->flush();
        }

        return $this->render('admin/reports/view.html.twig', [
            'report' => $report
        ]);
    }

    /**
     * @Route("/admin/report/{id}/close", name="aRClose", methods={"GET"})
     */
    public function aRClose(int $id)
    {
        $report = $this->rr->findOneBy(['id' => $id]);
        $report->setStatus('Closed');
        $this->em->flush();

        return $this->redirectToRoute('aReports', []);
    }

    /**
     * @Route("/admin/reasons", name="aReasons", methods={"GET"})
     */
    public function aReasons()
    {
        $this->checkPending();

        return $this->render('admin/reports/reasons/reasons.html.twig', [
            'reasons' => $this->rsr->findAll()
        ]);
    }

    /**
     * @Route("/admin/reasons/new", name="aNewReason", methods={"GET", "POST"})
     */
    public function aNewReason(Request $request)
    {
        $this->checkPending();

        $new = $this->createForm(NewReasonType::class);
        $new->handleRequest($request);

        if ($new->isSubmitted() && $new->isValid()) {
            try {
                $reason = $new->getData()['reason'];
                if ($this->rsr->findOneBy(['reason' => $reason])) throw new Exception("Reason already exists");
                else {
                    $reas = new \App\Entity\Reasons;
                    $reas->setReason($reason);

                    $this->em->persist($reas);
                    $this->em->flush();

                    return $this->redirectToRoute('aReasons', []);
                }
            } catch (QueryException $e) {
                throw new Exception("Something failed, try again. Error: {$e->getMessage()}");
            }
        }
        return $this->render('admin/reports/reasons/new.html.twig', [
            'create' => $new->createView()
        ]);
    }

    /**
     * @Route("/admin/reason/{id}/edit", name="aEditReason", methods={"GET", "POST"})
     */
    public function aEditReason(int $id, Request $request)
    {
        $this->checkPending();

        $edit = $this->createForm(EReasonType::class, null, ['id' => $id]);
        $edit->handleRequest($request);

        if ($edit->isSubmitted() && $edit->isValid()) {
            try {
                $reason = $edit->getData()['reason'];
                if ($this->rsr->findOneBy(['reason' => $reason])) throw new Exception("Reason already exists");
                else {
                    $reas = $this->rsr->findOneBy(['id' => $id]);
                    $reas->setReason($reason);

                    $this->em->flush();

                    return $this->redirectToRoute('aReasons', []);
                }
            } catch (QueryException $e) {
                throw new Exception("Something failed, try again. Error: {$e->getMessage()}");
            }
        }
        return $this->render('admin/reports/reasons/edit.html.twig', [
            'edit' => $edit->createView()
        ]);
    }


    /**
     * @Route("/admin/reason/{id}/remove", name="aRRemove", methods={"GET"})
     */
    public function aRRemove(int $id)
    {
        try {
            $reason = $this->rsr->findOneBy(['id' => $id]);
            if ($reason) {
                $this->em->remove($reason);
                $this->em->flush();
                return $this->redirectToRoute('aReasons', []);
            } else throw new NotFoundHttpException("Reason not found");
        } catch (QueryException $e) {
            throw new Exception("Something went wrong try again. Error: {$e->getMessage()}");
        }
    }

    /**
     * @Route("/admin/users", name="aUsers", methods={"GET"})
     */
    public function aUsers()
    {
        $this->checkPending();

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
            throw new Exception("Problem appeared during changing. Try again. Error code: {$e->getMessage()}");
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
            throw new Exception("Problem appeared during changing. Try again. Error code: {$e->getMessage()}");
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
            throw new Exception("Problem appeared during changing. Try again. Error code: {$e->getMessage()}");
        }
    }

    /**
     * @Route("/admin/uploads", name="aUploads", methods={"GET"})
     */
    public function aUploads(PaginatorInterface $paginator, Request $request)
    {
        $this->checkPending();

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
            throw new Exception("Something went wrong try again. Error: {$e->getMessage()}");
        }
    }

    /**
     * @Route("/admin/categories", name="aCategories", methods={"GET"})
     */
    public function aCategories(PaginatorInterface $paginator, Request $request)
    {
        $this->checkPending();

        return $this->render('admin/categories/categories.html.twig', [
            'categories' => $paginator->paginate($this->cr->findAll(), $request->query->getInt('page', 1), 15)
        ]);
    }

    /**
     * @Route("/admin/categories/create", name="aCNew", methods={"GET", "POST"})
     */
    public function aCNew(Request $request)
    {
        $this->checkPending();

        $create = $this->createForm(CategoryType::class);
        $create->handleRequest($request);

        if ($create->isSubmitted() && $create->isValid()) {
            try {
                $data = $create->getData();
                if (!$this->cr->findOneBy(['Name' => $data['Name']])) {
                    $cat = new \App\Entity\Categories;
                    $cat->setName($data['Name']);
                    foreach ($data['Tags'] as $t) $cat->addTag($t);

                    $this->em->persist($cat);
                    $this->em->flush();

                    return $this->redirectToRoute('aCategories', [
                        'page' => 1
                    ]);
                } else throw new Exception("Such category exists already");
            } catch (QueryException $e) {
                throw new Exception("Something went wrong try again. Error: {$e->getMessage()}");
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
        $this->checkPending();

        $edit = $this->createForm(CEditType::class, null, ['id' => $id]);
        $edit->handleRequest($request);
        $cat = $this->cr->findOneBy(['id' => $id]);

        if ($edit->isSubmitted() && $edit->isValid()) {
            try {
                if ($cat) {
                    $data = $edit->getData();
                    if (!$this->cr->findOneBy(['Name' => $data['Name']])) {
                        $cat->setName($data['Name']);
                        $cat->getTags()->clear();
                        foreach ($data['Tags'] as $t) $cat->addTag($t);

                        $this->em->flush();

                        return $this->redirectToRoute('aCategories', [
                            'page' => 1
                        ]);
                    } else throw new Exception("Such category exists already");
                } else throw new NotFoundHttpException("Category not found");
            } catch (QueryException $e) {
                throw new Exception("Something went wrong try again. Error: {$e->getMessage()}");
            }
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
            if ($category) {
                $this->em->remove($category);
                $this->em->flush();
                return $this->redirectToRoute('aCategories', [
                    'page' => 1
                ]);
            } else throw new NotFoundHttpException("Category not found");
        } catch (QueryException $e) {
            throw new Exception("Something went wrong try again. Error: {$e->getMessage()}");
        }
    }

    /**
     * @Route("/admin/tags", name="aTags", methods={"GET"})
     */
    public function aTags(PaginatorInterface $paginator, Request $request)
    {
        $this->checkPending();

        return $this->render('admin/tags/tags.html.twig', [
            'tags' => $paginator->paginate($this->tr->findAll(), $request->query->getInt('page', 1), 15)
        ]);
    }

    /**
     * @Route("/admin/tags/create", name="aTNew", methods={"GET", "POST"})
     */
    public function aTNew(Request $request)
    {
        $this->checkPending();

        $create = $this->createForm(TagsType::class);
        $create->handleRequest($request);

        if ($create->isSubmitted() && $create->isValid()) {
            try {
                $data = $create->getData();

                if ($this->tr->findOneBy(['Name' => $data['Name']])) throw new Exception("Such tag exists already");
                else {
                    $tag = new \App\Entity\Tags;
                    $tag->setName($data['Name']);

                    $this->em->persist($tag);
                    $this->em->flush();

                    return $this->redirectToRoute('aTags', [
                        'page' => 1
                    ]);
                }
            } catch (QueryException $e) {
                throw new Exception("Something went wrong try again. Error: {$e->getMessage()}");
            }
        }

        return $this->render('admin/tags/new.html.twig', [
            'create' => $create->createView()
        ]);
    }

    /**
     * @Route("/admin/tag/{id}/edit", name="aTEdit", methods={"GET", "POST"})
     */
    public function aTEdit(int $id, Request $request)
    {
        $this->checkPending();

        $edit = $this->createForm(TEditType::class, null, ['id' => $id]);
        $edit->handleRequest($request);

        if ($edit->isSubmitted() && $edit->isValid()) {
            try {
                $tag = $this->cr->findOneBy(['id' => $id]);

                if ($tag) {
                    $data = $edit->getData();
                    if (!$this->tr->findOneBy(['Name' => $data['Name']])) {
                        $tag->setName($data['Name']);
                        $this->em->flush();

                        return $this->redirectToRoute('aTags', [
                            'page' => 1
                        ]);
                    } else throw new Exception("Such tag exists already");
                } else throw new NotFoundHttpException("Tag not found");
            } catch (QueryException $e) {
                throw new Exception("Something went wrong try again. Error: {$e->getMessage()}");
            }
        }

        return $this->render('admin/tags/edit.html.twig', [
            'edit' => $edit->createView()
        ]);
    }

    /**
     * @Route("/admin/tag/{id}/remove", name="aTRemove", methods={"GET"})
     */
    public function aTRemove(int $id)
    {
        try {
            $tag = $this->tr->findOneBy(['id' => $id]);
            if ($tag) {
                $this->em->remove($tag);
                $this->em->flush();
                return $this->redirectToRoute('aTags', [
                    'page' => 1
                ]);
            } else throw new NotFoundHttpException("Tag not found");
        } catch (QueryException $e) {
            throw new Exception("Something went wrong try again. Error: {$e->getMessage()}");
        }
    }
}
