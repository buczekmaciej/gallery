<?php

namespace App\Controller;

use App\Form\ProfileType;
use App\Form\RegisterType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController
{
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/login", name="login", methods={"GET", "POST"})
     */
    public function login(AuthenticationUtils $au)
    {
        $username = $au->getLastUsername();
        $error = $au->getLastAuthenticationError();

        return $this->render('user/login.html.twig', [
            'error' => $error,
            'username' => $username
        ]);
    }

    /**
     * @Route("/register", name="register", methods={"GET", "POST"})
     */
    public function register($error = null, Request $request, UserRepository $ur, UserPasswordEncoderInterface $encoder)
    {
        $register = $this->createForm(RegisterType::class);
        $register->handleRequest($request);

        if ($register->isSubmitted() && $register->isValid()) {
            $data = $register->getData();

            if (filter_var($data['Email'], FILTER_VALIDATE_EMAIL)) {
                if (!$ur->checkRegister($data['Username'], $data['Email'])) {

                    try {
                        $new = new \App\Entity\User;
                        $new->setUsername($data['Username']);
                        $new->setPassword($encoder->encodePassword($new, $data['Password']));
                        $new->setEmail($data['Email']);
                        $new->setResetHash(\App\Services\Hash::generator($ur));
                        $new->setRoles(["ROLE_USER"]);

                        $this->em->persist($new);
                        $this->em->flush();

                        return $this->redirectToRoute('login', []);
                    } catch (\Doctrine\ORM\Query\QueryException $e) {
                        $error = "Something went wrong. Please try again. Error code: {$e->getMessage()}";
                    }
                } else $error = "Username or E-mail is taken";
            } else $error = "E-mail is not valid";
        }

        return $this->render('user/register.html.twig', [
            'register' => $register->createView(),
            'error' => $error
        ]);
    }

    /**
     * @Route("/logout", name="logout", methods={"GET"})
     */
    public function logout()
    {
        return $this->redirectToRoute('homepage', []);
    }

    /**
     * @Route("/color-schema-update", name="updateColorSchema", methods={"GET"})
     */
    public function updateColorSchema(UserRepository $ur, Request $request)
    {
        $user = $ur->findOneBy(['id' => $this->getUser()->getId()]);
        $user->setColorSchema($user->getColorSchema() == 'light' ? 'dark' : 'light');

        $this->em->flush();

        return $this->redirect($request->query->get('ref'));
    }

    /**
     * @Route("/profile", name="profile", methods={"GET", "POST"})
     */
    public function profile(Request $request, string $error = null, UserRepository $ur)
    {
        $update = $this->createForm(ProfileType::class);
        $update->handleRequest($request);

        if ($update->isSubmitted() && $update->isValid()) {
            $data = $update->getData();

            if (filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $user = $ur->findOneBy(['id' => $this->getUser()->getId()]);
                $user->setEmail($data['email']);

                $this->em->flush();
            } else $error = "E-mail is invalid";
        }


        return $this->render('user/profile.html.twig', [
            'user' => $this->getUser(),
            'update' => $update->createView(),
            'error' => $error
        ]);
    }
}
