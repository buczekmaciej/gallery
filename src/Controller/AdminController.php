<?php

namespace App\Controller;

use App\Repository\CategoriesRepository;
use App\Repository\GalleryRepository;
use App\Repository\ReportsRepository;
use App\Repository\TagsRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\QueryException;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}
