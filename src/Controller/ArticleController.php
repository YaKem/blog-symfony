<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="article")
     */
    public function home(ArticleRepository $repo)
    {
        $articles = $repo->findBy([], ['createdAt' => 'DESC']);

        return $this->render('article/home.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/article/{id<[/d]+>}", name="route_article")
     */
    public function show(ArticleRepository $repo)
    {
        $article = $repo->find($id);

        $this->render('article/show.html.twig', [compact('id')]);
    }

    /**
     * @Route("/article/{id<[/d]+>}/delete", name="route_delete")
     */
    public function delete(EntityManagerInterface $em)
    {
        $em->delete($article);
        $em->flush();
    }
}
