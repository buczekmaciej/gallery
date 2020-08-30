<?php

namespace App\Controller;

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
    public function register($error = null, Request $request, UserRepository $ur, UserPasswordEncoderInterface $encoder, EntityManagerInterface $em)
    {
        $register = $this->createForm(RegisterType::class);
        $register->handleRequest($request);

        if ($register->isSubmitted() && $register->isValid()) {
            $data = $register->getData();

            dump($data);
            if (filter_var($data['Email'], FILTER_VALIDATE_EMAIL)) {
                if (!$ur->checkRegister($data['Username'], $data['Email'])) {

                    try {
                        $new = new \App\Entity\User;
                        $new->setUsername($data['Username']);
                        $new->setPassword($encoder->encodePassword($new, $data['Password']));
                        $new->setEmail($data['Email']);
                        $new->setResetHash(\App\Services\Hash::generator($ur));
                        $new->setRoles(["ROLE_USER"]);

                        $em->persist($new);
                        $em->flush();

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
    public function updateColorSchema(UserRepository $ur, EntityManagerInterface $em, Request $request)
    {
        $user = $ur->findOneBy(['id' => $this->getUser()->getId()]);
        $user->setColorSchema($user->getColorSchema() == 'light' ? 'dark' : 'light');

        $em->flush();

        return $this->redirect($request->query->get('ref'));
    }
}
