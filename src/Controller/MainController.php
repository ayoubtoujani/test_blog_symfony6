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
use App\Form\ArticleFormType;
use App\Form\UpdateArticleFormType;
use Symfony\Component\HttpFoundation\RedirectResponse;



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
            $entityManager = $this->doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $success = 'Registration successful';
            $session->set('user', $user);

            return $this->redirectToRoute('app_login');
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
    #[Route('/articles/create', name: 'create_article', methods: ['GET', 'POST'])]
    public function createArticle(Request $request, SessionInterface $session): Response
    {
        $user = $session->get('user');
        if (!$user instanceof Auteur) {
            return $this->redirectToRoute('app_login');
        }

        $article = new Article();
        $form = $this->createForm(ArticleFormType::class, $article, ['validation_groups' => ['create']]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setDateCreation(new \DateTime());
            $findUser = $this->doctrine->getRepository(Auteur::class)->findOneBy(['id' => $user->getId()]);
            $article->setAuteur($findUser);

            $entityManager = $this->doctrine->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('articles');
        }

        return $this->render('Articles/createArticle.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    //geta ll articles by user

    #[Route('/my', name: 'my_articles', methods: ['GET', 'POST'])]
    public function myarticles(SessionInterface $session): Response
    {
        $user = $session->get('user');
        // Check if a user is logged in
        if ($user instanceof Auteur) {
            // Fetch publications by their owner
            $articles = $this->doctrine->getRepository(Article::class)->findBy(['Auteur' => $user->getId()]);
            return $this->render('Articles/myArticles.html.twig', [
                'articles' => $articles,
                'user' => $user,   
            ]);
        }
}

#[Route('/delete-article/{id}', name: 'delete_article', methods: ['GET', 'POST'])]
public function deletePublication($id,SessionInterface $session): RedirectResponse
{
    $article = $this->doctrine->getRepository(Article::class)->find($id);
    if (!$article) {
        return $this->redirectToRoute('my_articles');
    }
    $user = $session->get('user');
    if (!$user instanceof Auteur || $article->getAuteur()->getId() !== $user->getId()) {
        // add a message to the session
        return $this->redirectToRoute('my_articles', ['error' => 'You are not allowed to delete this article']);
    }
else{
    // Remove the article from the database
    $entityManager = $this->doctrine->getManager();
    $entityManager->remove($article);
    $entityManager->flush();
    return $this->redirectToRoute('my_articles');
}
}

#[Route('/update-article/{id}', name: 'update_article', methods: ['GET', 'POST'])]
public function updatePost($id , Request $request,SessionInterface $session): Response
{

    //
    $user = $session->get('user');
    if (!$user instanceof Auteur) {
        return $this->redirectToRoute('my_articles');
    }
    $article = $this->doctrine->getRepository(Article::class)->find($id);

    if (!$article) {
        return $this->redirectToRoute('articles');
    }

    $form = $this->createForm(UpdateArticleFormType::class, $article, ['validation_groups' => ['create']]);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager = $this->doctrine->getManager();
        $entityManager->flush();
        return $this->redirectToRoute('my_articles');
    }
    return $this->render('Articles/updateArticle.html.twig', [
        'form' => $form->createView(),
        'article' => $article,
        'user' => $user,
    ]);
}
//show article

#[Route('/show-article/{id}', name: 'show_article', methods: ['GET', 'POST'])]
public function showArticle($id, SessionInterface $session): Response
{
    $user = $session->get('user');
    if (!$user instanceof Auteur) {
        return $this->redirectToRoute('app_login');
    }
    $article = $this->doctrine->getRepository(Article::class)->find($id);
    if (!$article) {
        return $this->redirectToRoute('articles');
    }
    return $this->render('Articles/showArticle.html.twig', [
        'article' => $article,
        'user' => $user,
    ]);


}

}
