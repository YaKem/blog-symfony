<?php

namespace App\Controller;

use DateTime;
use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Category;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="app_home", methods="GET")
     */
    public function home(ArticleRepository $repo): Response
    {
        $articles = $repo->findBy([], ['createdAt' => 'DESC']);

        return $this->render('article/home.html.twig', [
            'articles' => $articles,
            'categories' => $this->categories
        ]);
    }

    /**
     * @Route("/article/{id<[\d]+>}", name="app_show", methods={"GET", "POST"})
     */
    public function show(Article $article, CommentRepository $repo, Request $request, EntityManagerInterface $em): Response
    {
        $comments = $repo->findBy(['article' => $article->getId()], ['createdAt' => 'DESC']);
        
        $comment = new Comment();

        $commentForm = $this->createForm(CommentType::class, $comment);

        $commentForm->handleRequest($request);

        if($commentForm->isSubmitted() && $commentForm->isValid()) {
            $comment->setCreatedAt(new \DateTime())
                    ->setArticle($article);
            $em->persist($comment);
            $em->flush();

            $this->addFlash('info', 'Votre commentaire a été ajouté!');

            $id = substr(strrchr($request->headers->get('referer'), '//'), 1);
            
            return $this->redirectToRoute('app_show', compact('id'));
        }
        return $this->render('article/show.html.twig', [
            'article' => $article,
            'comments' => $comments,
            'commentForm' => $commentForm->createView(),
            'categories' => $this->categories
        ]);
    }

    /**
     * @Route("/article/category/{id<[\d]+>}", name="app_article_by_category", methods={"GET", "POST"})
     */
    public function showByCategory(Category $category, ArticleRepository $repo, Request $request): Response
    {
        $results = $repo->findBy(['category' => $category->getId()], ['createdAt' => 'DESC']);

        return $this->render('article/search.html.twig', [
            'results' => $results,
            'categories' => $this->categories
        ]);
    }

    /**
    * @Route("/article/about", name="app_about", methods="GET")
    */
    public function about(): Response
    {
        return $this->render('article/about.html.twig', ['categories' => $this->categories]);
    }

    /**
    * @Route("/article/search", name="app_search", methods={"GET", "POST"})
    */
    public function search(Request $request, ArticleRepository $repo): Response
    {
        $word = $request->request->get('word');

        $results = $repo->findSearch($word);

        $word = strtolower($word);
        $wordf = ucfirst($word);

        return $this->render('article/search.html.twig', [
                'results' => $results,
                'word' => $word,
                'categories' => $this->categories
        ]);
    }
}
