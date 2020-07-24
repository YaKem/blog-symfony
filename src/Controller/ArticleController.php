<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Form\ArticleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="route_home", methods="GET")
     */
    public function home(ArticleRepository $repo): Response
    {
        $articles = $repo->findBy([], ['createdAt' => 'DESC']);

        return $this->render('article/home.html.twig', compact('articles'));
    }

    /**
     * @Route("/article/{id<[\d]+>}", name="route_show", methods="GET")
     */
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', compact('article'));
    }

    /**
     * @Route("/article/create", name="route_create", methods={"GET", "PUT"})
     */
    public function create(Request $request, EntityManagerInterface $em): response
    {
        $article = new Article;

        $form = $this->createForm(ArticleType::class, $article, [
            'method' => "PUT"
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em->persist($article);
            $em->flush();

            $this->addFlash('success', 'Nouvel article créé!');

            return $this->redirectToRoute('route_home');
        }

        return $this->render('article/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/article/{id<[\d]+>}/edit", name="route_edit", methods={"GET", "PUT"})
     */
    public function edit(Request $request, EntityManagerInterface $em, Article $article): Response
    {
        $form = $this->createForm(ArticleType::class, $article, [
            'method' => "PUT"
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            
        $this->addFlash('success', 'Votre article a été mis à jour!');

            return $this->redirectToRoute('route_home');
        }

        return $this->render('article/edit.html.twig', [
            'article' => $article,
            'form'    => $form->createView()
        ]);
    }

    /**
     * @Route("/article/{id<[\d]+>}/delete", name="route_delete", methods="DELETE")
     */
    public function delete(Request $request, EntityManagerInterface $em, Article $article): Response
    {
        $submittedToken = $request->request->get('csrf_token');

        if($this->isCsrfTokenValid('article_deletion' . $article->getId(), $submittedToken)) {
            $em->remove($article);
            $em->flush();
    
            $this->addFlash('success', 'Votre article a été supprimé!');
        }

        return $this->redirectToRoute('route_home');
    }

    /**
    * @Route("/article/about", name="route_about")
    */
    public function about()
    {
        return $this->render('article/about.html.twig');
    }
}
