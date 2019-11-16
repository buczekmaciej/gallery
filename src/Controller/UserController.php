<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Services\LoginStatus;
use App\Services\Hash;
use App\Form\LoginType;
use App\Form\RegisterType;
use App\Repository\UserRepository;
use App\Entity\User;

class UserController extends AbstractController
{
    private $uR;
    private $ls;
    private $session;
    private $hg;
    private $em;

    public function __construct(EntityManagerInterface $em, LoginStatus $ls, SessionInterface $session, Hash $hg, UserRepository $uR)
    {
        $this->uR = $uR;
        $this->session = $session;
        $this->ls = $ls->checkLoginStatus($session);
        $this->hg = $hg->generator($uR);
        $this->em = $em;
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(Request $request)
    {
        if($this->ls == true)
        {
            return $this->redirectToRoute('homepage', []);
        }

        // Login form call
        $loginForm = $this->createForm(LoginType::class);
        $loginForm->handleRequest($request);

        if($loginForm->isSubmitted() && $loginForm->isValid())
        {
            // Get data inserted into form
            $data = $loginForm->getData();

            // Get user with provided Login exists
            $user = $this->uR->findBy(['Login'=>$data->getLogin()]);

            if($user && $user[0]->getPassword() === md5($data->getPassword()))
            {
                $this->session->set('user', $user[0]);
                return $this->redirectToRoute('homepage', []);
            }
            else{
                $this->addFlash('danger', "User with this username doesn't exist or provided password is wrong. Try again!");
            }
        }

        return $this->render('user/login.html.twig', [
            'login'=>$loginForm->createView()
        ]);
    }

    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request)
    {
        if($this->ls == true)
        {
            return $this->redirectToRoute('homepage', []);
        }

        // Register form call
        $registerForm = $this->createForm(RegisterType::class);
        $registerForm->handleRequest($request);

        if($registerForm->isSubmitted() && $registerForm->isValid())
        {
            $user = $registerForm->getData();
            
            $taken = $this->uR->findBy(['Login'=>$user->getLogin()]);
            $used = $this->uR->findBy(['Email'=>$user->getEmail()]);
            if(!$taken && !$used)
            {
                $user->setPassword(md5($user->getPassword()));
                $user->setResetHash($this->hg);
                $user->setColorSchema('light');

                // Push User object to database
                $this->em->persist($user);
                $this->em->flush();

                return $this->redirectToRoute('login', []);
            }
            else{
                $this->addFlash('danger', 'Username or e-mail is already taken');
            }
        }

        return $this->render('user/register.html.twig', [
            'register'=>$registerForm->createView()
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {
        if($this->ls == false)
        {
            return $this->redirectToRoute('homepage', []);
        }

        $this->session->remove('user');

        return $this->redirectToRoute('homepage', []);
    }

    /**
     * @Route("/user/{id}/collection", name="myCollection", methods={"GET"})
     */
    public function myCollection(int $id)
    {
        if(!$this->ls)
        {
            return $this->redirectToRoute('homepage', []);
        }
        if($id !== $this->session->get('user')->getId())
        {
            return $this->redirectToRoute('myCollection', ['id'=>$this->session->get('user')->getId()]);
        }

        $saves = $this->uR->findBy(['id'=>$id])[0]->getCollection();
        foreach($saves as &$save)
        {
            $save->setImage(stream_get_contents($save->getImage()));
        }

        return $this->render('user/collection.html.twig', [
            'saves'=>$saves
        ]);
    }

    /**
     * @Route("/user/{id}/likes", name="myLikes", methods={"GET"})
     */
    public function myLikes(int $id)
    {
        if(!$this->ls)
        {
            return $this->redirectToRoute('homepage', []);
        }
        if($id !== $this->session->get('user')->getId())
        {
            return $this->redirectToRoute('myCollection', ['id'=>$this->session->get('user')->getId()]);
        }

        $likes = $this->uR->findBy(['id'=>$id])[0]->getLikes();
        foreach($likes as &$like)
        {
            $like->setImage(stream_get_contents($like->getImage()));
        }

        return $this->render('user/likes.html.twig', [
            'likes'=>$likes
        ]);
    }
}
