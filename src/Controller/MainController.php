<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Auteur;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Form\LoginFormType;
use App\Form\RegisterFormType;
use Doctrine\Persistence\ManagerRegistry;

class MainController extends AbstractController
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    #[Route('/login', name: 'app_login', methods:['POST', 'GET'])]
    public function login(Request $request, SessionInterface $session): Response
    {
        $error = '';
        $success = ''; 

        $state= $request->get('state');
        $loginForm = $this->createForm(LoginFormType::class);
        $loginForm->handleRequest($request);

        $registerForm = $this->createForm(RegisterFormType::class);
        $registerForm->handleRequest($request);

        if ($loginForm->isSubmitted() && $loginForm->isValid()) {
            $user = $loginForm->getData();
            
            $email = $user->getEmail();
            $password = $user->getPassword();

            $auteurRepository = $this->doctrine->getRepository(Auteur::class);
            $foundUser = $auteurRepository->findOneBy(['email' => $email]);

            if ($foundUser && $foundUser->getPassword() === $password) {
                $success = 'Login successful';
                $session->set('user', $foundUser);

                return $this->redirectToRoute('articles');
                
            } else {
                $error = 'Invalid email or password';
            }
        }

        if ($registerForm->isSubmitted() && $registerForm->isValid()) {
            $user = $registerForm->getData();
            // Additional validation or processing if needed before persisting
            $entityManager = $this->doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $success = 'Registration successful';
            $session->set('user', $user);

            return $this->redirectToRoute('articles');
        }
        
        return $this->render('login/login.html.twig', [
            'loginForm' => $loginForm->createView(),
            'registerForm' => $registerForm->createView(),
            'state' => $state,
            'error' => $error,
            'success' => $success,
        ]);
    }
    //find all articles
    #[Route('/articles', name: 'articles', methods: ['GET', 'POST'])]
    public function articles(SessionInterface $session): Response
    {
        $user = $session->get('user');
        if ($user instanceof Auteur){

            $articles = $this->doctrine->getRepository(Article::class)->findAll();
            return $this->render('Articles/listArticle.html.twig', [
                'articles' => $articles,
                'user' => $user
            ]);
        }
       
    }
}
